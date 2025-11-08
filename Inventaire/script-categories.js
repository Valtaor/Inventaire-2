(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var settings = window.inventorySettings || {};
    var ajaxUrl = settings.ajaxUrl || window.ajaxurl;
    if (!ajaxUrl) {
      return;
    }

    // Éléments DOM - Catégories
    var categoryForm = document.getElementById('inventory-category-form');
    var categoryIdInput = document.getElementById('category-id');
    var categoryNameInput = document.getElementById('category-name');
    var categoryColorInput = document.getElementById('category-color');
    var categoryIconInput = document.getElementById('category-icon');
    var categoryList = document.getElementById('inventory-category-list');
    var resetCategoryButton = document.getElementById('reset-category-form');

    // Éléments DOM - Tags
    var tagForm = document.getElementById('inventory-tag-form');
    var tagIdInput = document.getElementById('tag-id');
    var tagNameInput = document.getElementById('tag-name');
    var tagList = document.getElementById('inventory-tag-list');
    var resetTagButton = document.getElementById('reset-tag-form');

    var allCategories = [];
    var allTags = [];

    // Fonction d'affichage de toast (utilise la fonction globale si disponible)
    function showToast(message, type) {
      if (window.showToast) {
        window.showToast(message, type);
      } else {
        console.log(type + ': ' + message);
      }
    }

    // ========================================
    // Gestion des catégories
    // ========================================

    function fetchCategories() {
      var url = ajaxUrl;
      if (url.indexOf('?') === -1) {
        url += '?action=get_categories';
      } else {
        url += '&action=get_categories';
      }

      return fetch(url, {
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network error');
          }
          return response.json();
        })
        .then(function (json) {
          if (!json || !json.success) {
            throw new Error((json && json.data && json.data.message) || 'Erreur');
          }
          allCategories = json.data || [];
          renderCategories();
        })
        .catch(function (error) {
          showToast('Impossible de charger les catégories.', 'error');
          console.error(error);
        });
    }

    function renderCategories() {
      if (!categoryList) {
        return;
      }
      categoryList.innerHTML = '';

      if (allCategories.length === 0) {
        var emptyLi = document.createElement('li');
        emptyLi.className = 'taxonomy-empty';
        emptyLi.textContent = 'Aucune catégorie créée.';
        categoryList.appendChild(emptyLi);
        return;
      }

      allCategories.forEach(function (category) {
        var li = document.createElement('li');
        li.className = 'taxonomy-item';
        li.dataset.id = category.id;

        var indicator = document.createElement('span');
        indicator.className = 'taxonomy-color-indicator';
        indicator.style.backgroundColor = category.color || '#c47b83';

        var content = document.createElement('div');
        content.className = 'taxonomy-content';

        var nameSpan = document.createElement('span');
        nameSpan.className = 'taxonomy-name';
        if (category.icon) {
          nameSpan.textContent = category.icon + ' ' + category.name;
        } else {
          nameSpan.textContent = category.name;
        }

        content.appendChild(nameSpan);

        var actions = document.createElement('div');
        actions.className = 'taxonomy-actions';

        var editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'taxonomy-btn taxonomy-btn-edit';
        editButton.textContent = 'Modifier';
        editButton.addEventListener('click', function () {
          editCategory(category);
        });

        var deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'taxonomy-btn taxonomy-btn-delete';
        deleteButton.textContent = 'Supprimer';
        deleteButton.addEventListener('click', function () {
          deleteCategory(category.id);
        });

        actions.appendChild(editButton);
        actions.appendChild(deleteButton);

        li.appendChild(indicator);
        li.appendChild(content);
        li.appendChild(actions);

        categoryList.appendChild(li);
      });
    }

    function submitCategory(event) {
      event.preventDefault();

      var id = categoryIdInput ? categoryIdInput.value : '';
      var name = categoryNameInput ? categoryNameInput.value.trim() : '';
      var color = categoryColorInput ? categoryColorInput.value : '#c47b83';
      var icon = categoryIconInput ? categoryIconInput.value.trim() : '';

      if (!name) {
        showToast('Le nom de la catégorie est requis.', 'error');
        return;
      }

      var formData = new FormData();
      formData.append('action', id ? 'edit_category' : 'add_category');
      formData.append('name', name);
      formData.append('color', color);
      formData.append('icon', icon);
      if (id) {
        formData.append('id', id);
      }

      fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network error');
          }
          return response.json();
        })
        .then(function (json) {
          if (!json || !json.success) {
            throw new Error((json && json.data && json.data.message) || 'Erreur');
          }
          showToast(id ? 'Catégorie mise à jour.' : 'Catégorie créée.', 'success');
          resetCategoryForm();
          fetchCategories();
        })
        .catch(function (error) {
          showToast('Erreur lors de l\'enregistrement de la catégorie. ' + (error && error.message ? error.message : ''), 'error');
        });
    }

    function editCategory(category) {
      if (categoryIdInput) {
        categoryIdInput.value = category.id;
      }
      if (categoryNameInput) {
        categoryNameInput.value = category.name;
      }
      if (categoryColorInput) {
        categoryColorInput.value = category.color || '#c47b83';
      }
      if (categoryIconInput) {
        categoryIconInput.value = category.icon || '';
      }
      if (categoryNameInput) {
        categoryNameInput.focus();
      }
    }

    function deleteCategory(id) {
      if (!confirm('Supprimer cette catégorie ?')) {
        return;
      }

      var formData = new FormData();
      formData.append('action', 'delete_category');
      formData.append('id', id);

      fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network error');
          }
          return response.json();
        })
        .then(function (json) {
          if (!json || !json.success) {
            throw new Error((json && json.data && json.data.message) || 'Erreur');
          }
          showToast('Catégorie supprimée.', 'success');
          fetchCategories();
        })
        .catch(function (error) {
          showToast('Erreur lors de la suppression. ' + (error && error.message ? error.message : ''), 'error');
        });
    }

    function resetCategoryForm() {
      if (categoryForm) {
        categoryForm.reset();
      }
      if (categoryIdInput) {
        categoryIdInput.value = '';
      }
    }

    // ========================================
    // Gestion des tags
    // ========================================

    function fetchTags() {
      var url = ajaxUrl;
      if (url.indexOf('?') === -1) {
        url += '?action=get_tags';
      } else {
        url += '&action=get_tags';
      }

      return fetch(url, {
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network error');
          }
          return response.json();
        })
        .then(function (json) {
          if (!json || !json.success) {
            throw new Error((json && json.data && json.data.message) || 'Erreur');
          }
          allTags = json.data || [];
          renderTags();
        })
        .catch(function (error) {
          showToast('Impossible de charger les tags.', 'error');
          console.error(error);
        });
    }

    function renderTags() {
      if (!tagList) {
        return;
      }
      tagList.innerHTML = '';

      if (allTags.length === 0) {
        var emptyLi = document.createElement('li');
        emptyLi.className = 'taxonomy-empty';
        emptyLi.textContent = 'Aucun tag créé.';
        tagList.appendChild(emptyLi);
        return;
      }

      allTags.forEach(function (tag) {
        var li = document.createElement('li');
        li.className = 'taxonomy-item';
        li.dataset.id = tag.id;

        var content = document.createElement('div');
        content.className = 'taxonomy-content';

        var nameSpan = document.createElement('span');
        nameSpan.className = 'taxonomy-name';
        nameSpan.textContent = tag.name;

        content.appendChild(nameSpan);

        var actions = document.createElement('div');
        actions.className = 'taxonomy-actions';

        var editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'taxonomy-btn taxonomy-btn-edit';
        editButton.textContent = 'Modifier';
        editButton.addEventListener('click', function () {
          editTag(tag);
        });

        var deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'taxonomy-btn taxonomy-btn-delete';
        deleteButton.textContent = 'Supprimer';
        deleteButton.addEventListener('click', function () {
          deleteTag(tag.id);
        });

        actions.appendChild(editButton);
        actions.appendChild(deleteButton);

        li.appendChild(content);
        li.appendChild(actions);

        tagList.appendChild(li);
      });
    }

    function submitTag(event) {
      event.preventDefault();

      var id = tagIdInput ? tagIdInput.value : '';
      var name = tagNameInput ? tagNameInput.value.trim() : '';

      if (!name) {
        showToast('Le nom du tag est requis.', 'error');
        return;
      }

      var formData = new FormData();
      formData.append('action', id ? 'edit_tag' : 'add_tag');
      formData.append('name', name);
      if (id) {
        formData.append('id', id);
      }

      fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network error');
          }
          return response.json();
        })
        .then(function (json) {
          if (!json || !json.success) {
            throw new Error((json && json.data && json.data.message) || 'Erreur');
          }
          showToast(id ? 'Tag mis à jour.' : 'Tag créé.', 'success');
          resetTagForm();
          fetchTags();
        })
        .catch(function (error) {
          showToast('Erreur lors de l\'enregistrement du tag. ' + (error && error.message ? error.message : ''), 'error');
        });
    }

    function editTag(tag) {
      if (tagIdInput) {
        tagIdInput.value = tag.id;
      }
      if (tagNameInput) {
        tagNameInput.value = tag.name;
      }
      if (tagNameInput) {
        tagNameInput.focus();
      }
    }

    function deleteTag(id) {
      if (!confirm('Supprimer ce tag ?')) {
        return;
      }

      var formData = new FormData();
      formData.append('action', 'delete_tag');
      formData.append('id', id);

      fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network error');
          }
          return response.json();
        })
        .then(function (json) {
          if (!json || !json.success) {
            throw new Error((json && json.data && json.data.message) || 'Erreur');
          }
          showToast('Tag supprimé.', 'success');
          fetchTags();
        })
        .catch(function (error) {
          showToast('Erreur lors de la suppression. ' + (error && error.message ? error.message : ''), 'error');
        });
    }

    function resetTagForm() {
      if (tagForm) {
        tagForm.reset();
      }
      if (tagIdInput) {
        tagIdInput.value = '';
      }
    }

    // ========================================
    // Event Listeners
    // ========================================

    if (categoryForm) {
      categoryForm.addEventListener('submit', submitCategory);
    }

    if (resetCategoryButton) {
      resetCategoryButton.addEventListener('click', resetCategoryForm);
    }

    if (tagForm) {
      tagForm.addEventListener('submit', submitTag);
    }

    if (resetTagButton) {
      resetTagButton.addEventListener('click', resetTagForm);
    }

    // ========================================
    // Initialisation
    // ========================================

    // Charger les catégories et tags seulement si on est sur la page catégories
    if (categoryList || tagList) {
      fetchCategories();
      fetchTags();
    }

    // Exposer les fonctions globalement pour les utiliser dans d'autres scripts
    window.inventoryCategories = {
      getAll: function() { return allCategories; },
      refresh: fetchCategories
    };

    window.inventoryTags = {
      getAll: function() { return allTags; },
      refresh: fetchTags
    };
  });
})();
