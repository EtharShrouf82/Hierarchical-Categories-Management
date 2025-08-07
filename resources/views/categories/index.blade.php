<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hierarchical Categories Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Nice Select -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" rel="stylesheet">
    
    <style>
        .category-tree {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .category-item {
            border: 1px solid #e3e3e3;
            border-radius: 6px;
            margin-bottom: 8px;
            background: #fff;
            transition: all 0.3s ease;
        }
        
        .category-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .category-header {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
            border-radius: 6px 6px 0 0;
        }
        
        .category-header.inactive {
            background-color: #f8f9fa;
            opacity: 0.7;
        }
        
        .drag-handle {
            cursor: grab;
            color: #6c757d;
            margin-right: 10px;
        }
        
        .drag-handle:active {
            cursor: grabbing;
        }
        
        .category-children {
            padding-left: 30px;
            border-left: 2px solid #e9ecef;
            margin-left: 20px;
        }
        
        .collapse-btn {
            background: none;
            border: none;
            font-size: 0.9rem;
            color: #6c757d;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        
        .collapse-btn.collapsed {
            transform: rotate(-90deg);
        }
        
        .btn-action {
            margin-left: 5px;
            padding: 6px 10px;
            font-size: 0.8rem;
        }
        
        .category-info {
            flex: 1;
            margin-left: 10px;
        }
        
        .category-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }
        
        .category-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .category-counters {
            display: flex;
            gap: 10px;
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .counter {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 10px;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .search-filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .bulk-actions {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .bulk-actions.show {
            display: block;
        }
        
        .checkbox-wrapper {
            margin-right: 10px;
        }
        
        .tooltip-custom {
            position: relative;
            cursor: help;
        }
        
        .tooltip-custom:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }
        
        .sortable-ghost {
            opacity: 0.5;
            background: #e3f2fd !important;
        }
        
        .sortable-chosen {
            background: #fff3cd !important;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .nice-select {
            width: 100%;
            z-index: 9999 !important;
        }
        
        .nice-select .list {
            z-index: 10000 !important;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            background: white !important;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: none;
        }
        
        .nice-select.open .list,
        .nice-select:hover .list,
        .nice-select.focus .list {
            display: block !important;
            z-index: 10000 !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .nice-select .list li {
            display: block !important;
        }
        
        .nice-select .option {
            padding: 8px 12px;
            white-space: nowrap;
        }
        
        .nice-select .option.hierarchy-level-1 {
            padding-left: 24px;
        }
        
        .nice-select .option.hierarchy-level-2 {
            padding-left: 36px;
        }
        
        .nice-select .option.hierarchy-level-3 {
            padding-left: 48px;
        }
        
        .nice-select .option.hierarchy-level-4 {
            padding-left: 60px;
        }
        
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal {
            z-index: 1050;
        }
        
        .modal-backdrop {
            z-index: 1040;
        }
        
        .modal .nice-select {
            position: relative;
        }
        
        .modal .nice-select .list {
            position: absolute !important;
            z-index: 99999 !important;
        }
        
        .form-floating {
            margin-bottom: 15px;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        /* SweetAlert custom styles */
        .swal-wide {
            width: 600px !important;
        }
        
        .swal2-popup .text-start {
            text-align: left !important;
        }
        
        .swal2-popup .list-unstyled {
            list-style: none;
            padding-left: 0;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="fas fa-sitemap text-primary me-2"></i>
                    Hierarchical Categories Management
                </h2>
                <p class="text-muted mb-0">Manage your categories in a tree-like structure</p>
            </div>
            <div class="btn-group">
                <button class="btn btn-outline-primary btn-sm" onclick="expandAll()">
                    <i class="fas fa-expand"></i> Expand All
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="collapseAll()">
                    <i class="fas fa-compress"></i> Collapse All
                </button>
                <button class="btn btn-outline-info btn-sm" onclick="resetExpandState()">
                    <i class="fas fa-undo"></i> Reset State
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating position-relative">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search categories..." autocomplete="off">
                        <label for="searchInput">Search Categories</label>
                        <button type="button" id="clearSearch" class="btn btn-sm btn-outline-secondary position-absolute" 
                                style="right: 10px; top: 50%; transform: translateY(-50%); display: none; z-index: 10; border: none; background: transparent; color: #6c757d;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <label for="statusFilter">Status Filter</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-select" id="levelFilter">
                            <option value="">All Levels</option>
                            <option value="0">Root Level</option>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3+</option>
                        </select>
                        <label for="levelFilter">Level Filter</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100" onclick="clearAllFilters()">
                        <i class="fas fa-undo"></i> Clear All
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold">Selected: <span id="selectedCount">0</span> categories</span>
                </div>
                <div>
                    <button class="btn btn-warning btn-sm me-2" onclick="bulkMove()">
                        <i class="fas fa-exchange-alt"></i> Move to Parent
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Categories Tree -->
        <div class="category-tree">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="mb-0">Categories Tree</h5>
                <div>
                    <button class="btn btn-success btn-sm" onclick="showAddModal()">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                    <button class="btn btn-warning btn-sm ms-2" onclick="testModal()">
                        <i class="fas fa-bug"></i> Test Modal
                    </button>
                    <button class="btn btn-info btn-sm ms-2" onclick="testEditModal()">
                        <i class="fas fa-edit"></i> Test Edit
                    </button>
                    <button class="btn btn-secondary btn-sm ms-2" onclick="listAllCategoryIds()">
                        <i class="fas fa-list"></i> List IDs
                    </button>
                </div>
            </div>
            <div id="categoryList" class="p-3"></div>
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="categoryId">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="categoryTitle" placeholder="Category Name" required>
                                    <label for="categoryTitle">Category Name *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="categoryOrder" placeholder="Order" value="1" min="1" required>
                                    <label for="categoryOrder">Order *</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="parentCategory">
                                        <option value="">No Parent (Root Category)</option>
                                    </select>
                                    <label for="parentCategory">Parent Category</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select nice-select-target" id="categoryStatus">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <label for="categoryStatus">Status</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategory()">Save Category</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Move Modal -->
    <div class="modal fade" id="bulkMoveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Move Categories to New Parent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating">
                        <select class="form-select" id="newParentCategory">
                            <option value="">No Parent (Root Level)</option>
                        </select>
                        <label for="newParentCategory">New Parent Category</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="confirmBulkMove()">Move Categories</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        let categories = @json($categories);
        let selectedCategories = new Set();
        let collapsedCategories = new Set(); // Changed to track collapsed instead of expanded
        
        // Ensure global access
        window.categories = categories;

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, categories:', categories);
            console.log('Categories count:', categories.length);
            console.log('First category:', categories[0]);
            console.log('Global window.categories:', window.categories);
            
            loadExpandState();
            renderCategories();
            initializeSortables();
            loadHierarchicalCategories();
            
            // Add event delegation for dynamically generated buttons
            document.addEventListener('click', function(e) {
                // Handle edit button clicks
                if (e.target.closest('.btn-edit')) {
                    e.preventDefault();
                    const button = e.target.closest('.btn-edit');
                    const categoryId = button.getAttribute('data-category-id');
                    console.log('Edit button clicked for category ID:', categoryId);
                    if (categoryId) {
                        showEditModal(parseInt(categoryId));
                    }
                }
                
                // Handle add button clicks
                if (e.target.closest('.btn-add')) {
                    e.preventDefault();
                    const button = e.target.closest('.btn-add');
                    const categoryId = button.getAttribute('data-category-id');
                    console.log('Add button clicked for category ID:', categoryId);
                    if (categoryId) {
                        showAddModal(parseInt(categoryId));
                    }
                }
                
                // Handle delete button clicks
                if (e.target.closest('.btn-delete')) {
                    e.preventDefault();
                    const button = e.target.closest('.btn-delete');
                    const categoryId = button.getAttribute('data-category-id');
                    console.log('Delete button clicked for category ID:', categoryId);
                    if (categoryId) {
                        deleteCategory(parseInt(categoryId));
                    }
                }
            });
            
            // Live search functionality
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const levelFilter = document.getElementById('levelFilter');
            const clearSearchBtn = document.getElementById('clearSearch');
            
            let searchTimeout;
            
            // Search as you type with debouncing
            searchInput.addEventListener('input', function() {
                const value = this.value.trim();
                
                // Show/hide clear button
                clearSearchBtn.style.display = value ? 'block' : 'none';
                
                // Debounce search to avoid too many requests
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchCategories();
                }, 300); // Wait 300ms after user stops typing
            });
            
            // Live filter updates
            statusFilter.addEventListener('change', searchCategories);
            levelFilter.addEventListener('change', searchCategories);
            
            // Clear search functionality
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                searchCategories();
                searchInput.focus();
            });
        });

        function loadExpandState() {
            fetch('{{ route("categories.getExpandState") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.expanded_categories) {
                        // Convert old expanded state to collapsed state (invert the logic)
                        collapsedCategories = new Set(data.collapsed_categories || []);
                    }
                });
        }

        function saveExpandState() {
            fetch('{{ route("categories.saveExpandState") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    collapsed_categories: Array.from(collapsedCategories)
                })
            });
        }

        function renderCategories() {
            console.log('renderCategories called with', categories.length, 'categories');
            const container = document.getElementById('categoryList');
            container.innerHTML = '';
            categories.forEach(cat => {
                container.appendChild(buildCategory(cat));
            });
            console.log('Categories rendered successfully');
        }

        function buildCategory(cat, level = 0) {
            const card = document.createElement('div');
            card.classList.add('category-item');
            card.dataset.id = cat.id;
            card.dataset.level = level;

            const hasChildren = cat.children_recursive && cat.children_recursive.length > 0;
            // Default to expanded unless explicitly collapsed
            const isExpanded = !collapsedCategories.has(cat.id);
            const totalDescendants = countTotalDescendants(cat);
            const directChildren = cat.children_recursive ? cat.children_recursive.length : 0;

            const header = document.createElement('div');
            header.className = `category-header ${cat.status ? '' : 'inactive'}`;
            header.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" class="form-check-input category-checkbox" 
                               data-id="${cat.id}" onchange="toggleCategorySelection(${cat.id})">
                    </div>
                    <i class="fas fa-grip-vertical drag-handle"></i>
                    ${hasChildren ? `<button class="collapse-btn ${isExpanded ? '' : 'collapsed'}" 
                        onclick="toggleCategory(${cat.id})">
                        <i class="fas fa-chevron-down"></i>
                    </button>` : ''}
                    <div class="category-info">
                        <div class="category-title">
                            ${cat.title_translation?.title || 'Untitled'}
                            <span class="status-badge ${cat.status ? 'status-active' : 'status-inactive'}">
                                ${cat.status ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        <div class="category-meta">
                            <span class="tooltip-custom" data-tooltip="Created: ${cat.created_at} | Modified: ${cat.updated_at}">
                                <i class="fas fa-clock"></i> ${cat.user?.name || 'Unknown'}
                            </span>
                            <div class="category-counters">
                                <span class="counter">Direct: ${directChildren}</span>
                                <span class="counter">Total: ${totalDescendants}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-action btn-add" data-category-id="${cat.id}" title="Add Child">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-action btn-edit" data-category-id="${cat.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-action btn-delete" data-category-id="${cat.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            card.appendChild(header);

            if (hasChildren) {
                const childrenContainer = document.createElement('div');
                childrenContainer.className = `category-children collapse ${isExpanded ? 'show' : ''}`;
                childrenContainer.id = `children-${cat.id}`;
                childrenContainer.dataset.parentId = cat.id;

                cat.children_recursive.forEach(child => {
                    childrenContainer.appendChild(buildCategory(child, level + 1));
                });

                card.appendChild(childrenContainer);
            }

            return card;
        }

        function countTotalDescendants(category) {
            let count = 0;
            if (category.children_recursive) {
                count += category.children_recursive.length;
                category.children_recursive.forEach(child => {
                    count += countTotalDescendants(child);
                });
            }
            return count;
        }

        function toggleCategory(categoryId) {
            const childrenContainer = document.getElementById(`children-${categoryId}`);
            const collapseBtn = childrenContainer.previousElementSibling.querySelector('.collapse-btn');
            
            if (collapsedCategories.has(categoryId)) {
                // Currently collapsed, so expand it
                collapsedCategories.delete(categoryId);
                childrenContainer.classList.add('show');
                collapseBtn.classList.remove('collapsed');
            } else {
                // Currently expanded, so collapse it
                collapsedCategories.add(categoryId);
                childrenContainer.classList.remove('show');
                collapseBtn.classList.add('collapsed');
            }
            
            saveExpandState();
        }

        function expandAll() {
            document.querySelectorAll('.category-children').forEach(container => {
                const categoryId = parseInt(container.dataset.parentId);
                collapsedCategories.delete(categoryId); // Remove from collapsed set
                container.classList.add('show');
                container.previousElementSibling.querySelector('.collapse-btn').classList.remove('collapsed');
            });
            saveExpandState();
        }

        function collapseAll() {
            document.querySelectorAll('.category-children').forEach(container => {
                const categoryId = parseInt(container.dataset.parentId);
                collapsedCategories.add(categoryId); // Add to collapsed set
                container.classList.remove('show');
                container.previousElementSibling.querySelector('.collapse-btn').classList.add('collapsed');
            });
            saveExpandState();
        }

        function resetExpandState() {
            fetch('{{ route("categories.resetExpandState") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                collapsedCategories.clear(); // Clear collapsed set (so all are expanded)
                renderCategories();
                Swal.fire('Success', 'Expand state reset successfully - all categories expanded', 'success');
            });
        }

        function initializeSortables() {
            new Sortable(document.getElementById('categoryList'), {
                group: 'categories',
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: updateOrderAjax
            });

            document.querySelectorAll('.category-children').forEach(container => {
                new Sortable(container, {
                    group: 'categories',
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: updateOrderAjax
                });
            });
        }

        function updateOrderAjax(evt) {
            const movedId = parseInt(evt.item.dataset.id);
            const newParentIdAttr = evt.to.getAttribute('data-parent-id');
            const newParentId = newParentIdAttr ? parseInt(newParentIdAttr) : null;
            const newIndex = evt.newIndex + 1;

            const data = {
                id: movedId,
                parent_id: newParentId,
                ord: newIndex
            };

            fetch('{{ route("categories.updateOrder") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Category order updated successfully', 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }

        function toggleCategorySelection(categoryId) {
            if (selectedCategories.has(categoryId)) {
                selectedCategories.delete(categoryId);
            } else {
                selectedCategories.add(categoryId);
            }
            updateBulkActions();
        }

        function updateBulkActions() {
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = selectedCategories.size;
            
            if (selectedCategories.size > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }

        function loadHierarchicalCategories() {
            fetch('{{ route("categories.hierarchical") }}')
                .then(response => response.json())
                .then(data => {
                    const parentSelect = document.getElementById('parentCategory');
                    const newParentSelect = document.getElementById('newParentCategory');
                    
                    // Clear existing options (except first one)
                    while (parentSelect.children.length > 1) {
                        parentSelect.removeChild(parentSelect.lastChild);
                    }
                    while (newParentSelect.children.length > 1) {
                        newParentSelect.removeChild(newParentSelect.lastChild);
                    }
                    
                    data.forEach(category => {
                        const option = new Option(category.title, category.id);
                        const option2 = new Option(category.title, category.id);
                        
                        // Add hierarchy level class
                        option.className = `hierarchy-level-${category.level}`;
                        option2.className = `hierarchy-level-${category.level}`;
                        
                        parentSelect.add(option);
                        newParentSelect.add(option2);
                    });
                    
                    console.log('Loaded', data.length, 'categories for dropdown');
                    
                    // Destroy existing nice select if it exists
                    $('.nice-select-initialized').each(function() {
                        $(this).niceSelect('destroy');
                    });
                    
                    // Initialize nice select with proper settings
                    console.log('Initializing nice select for parent categories');
                    
                    // Force destroy and recreate
                    setTimeout(() => {
                        $('#parentCategory, #newParentCategory').niceSelect({
                            searchable: false,  // Disable search temporarily to test
                            placeholder: 'Select parent category...'
                        });
                        
                        // Initialize status select without search
                        console.log('Initializing nice select for status');
                        $('#categoryStatus').niceSelect({
                            placeholder: 'Select status...'
                        });
                        
                        console.log('Nice select initialization complete');
                        
                        // Add click event listener to debug
                        $('.nice-select').on('click', function() {
                            console.log('Nice select clicked:', this);
                            console.log('Has open class:', $(this).hasClass('open'));
                        });
                    }, 100);
                });
        }

        function showAddModal(parentId = null) {
            document.getElementById('modalTitle').textContent = 'Add New Category';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryOrder').value = '1';
            
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
            
            // Wait for modal to be shown before updating nice select
            document.getElementById('categoryModal').addEventListener('shown.bs.modal', function() {
                const parentSelect = document.getElementById('parentCategory');
                if (parentId) {
                    console.log('Setting parent category to:', parentId);
                    parentSelect.value = parentId;
                    // Update nice select display
                    $('#parentCategory').niceSelect('update');
                } else {
                    parentSelect.value = '';
                    $('#parentCategory').niceSelect('update');
                }
            }, { once: true });
        }

        function showEditModal(categoryId) {
            console.log('showEditModal called with ID:', categoryId);
            
            try {
                const category = findCategory(categoryId);
                console.log('Found category:', category);
                
                if (!category) {
                    console.error('Category not found for ID:', categoryId);
                    alert('Category not found for ID: ' + categoryId);
                    return;
                }

                // Set modal title and form values
                console.log('Setting form values...');
                document.getElementById('modalTitle').textContent = 'Edit Category';
                document.getElementById('categoryId').value = category.id;
                document.getElementById('categoryTitle').value = category.title_translation?.title || '';
                document.getElementById('categoryOrder').value = category.ord;
                document.getElementById('parentCategory').value = category.parent_id || '';
                document.getElementById('categoryStatus').value = category.status ? '1' : '0';
                
                console.log('Form values set. Opening modal...');
                console.log('Opening edit modal for category:', category.title_translation?.title);
                
                // Check if modal element exists
                const modalElement = document.getElementById('categoryModal');
                if (!modalElement) {
                    console.error('Modal element not found!');
                    alert('Modal element not found!');
                    return;
                }
                
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Modal.show() called');
                
                // Wait for modal to be shown before updating nice select
                modalElement.addEventListener('shown.bs.modal', function() {
                    // Update nice select displays
                    $('#parentCategory').niceSelect('update');
                    $('#categoryStatus').niceSelect('update');
                    console.log('Updated nice select for edit modal');
                }, { once: true });
                
            } catch (error) {
                console.error('Error in showEditModal:', error);
                alert('Error in showEditModal: ' + error.message);
            }
        }

        function findCategory(categoryId, categories) {
            // Ensure we're using the global categories if none provided
            if (!categories) {
                categories = window.categories || [];
                console.log('Using global categories:', categories?.length);
            }
            console.log('=== findCategory Debug ===');
            console.log('Searching for ID:', categoryId, 'Type:', typeof categoryId);
            console.log('Categories array:', categories);
            console.log('Categories length:', categories?.length);
            
            if (!categories || categories.length === 0) {
                console.log('No categories to search in');
                return null;
            }
            
            // Convert categoryId to number for comparison
            const targetId = parseInt(categoryId);
            console.log('Target ID (converted to int):', targetId);
            
            // First, let's dump all category IDs we have
            console.log('Available category IDs:');
            categories.forEach((cat, index) => {
                console.log(`[${index}] ID: ${cat.id} (${typeof cat.id}) - ${cat.title_translation?.title}`);
            });
            
            for (let category of categories) {
                const categoryIdNum = parseInt(category.id);
                console.log(`Checking: ${category.id} (${typeof category.id}) vs target: ${targetId} (${typeof targetId})`);
                console.log(`Parsed: ${categoryIdNum} === ${targetId}? ${categoryIdNum === targetId}`);
                
                // Use strict equality after converting both to numbers
                if (categoryIdNum === targetId) {
                    console.log('✓ MATCH FOUND:', category);
                    return category;
                }
                
                // Search in children recursively
                if (category.children_recursive && category.children_recursive.length > 0) {
                    console.log('Searching in', category.children_recursive.length, 'children of', category.title_translation?.title);
                    const found = findCategory(targetId, category.children_recursive);
                    if (found) {
                        console.log('Found in children:', found);
                        return found;
                    }
                }
            }
            console.log('✗ Category not found for ID:', categoryId);
            return null;
        }

        function saveCategory() {
            const formData = {
                title: document.getElementById('categoryTitle').value,
                parent_id: document.getElementById('parentCategory').value || null,
                ord: parseInt(document.getElementById('categoryOrder').value),
                status: document.getElementById('categoryStatus').value === '1'
            };

            const categoryId = document.getElementById('categoryId').value;
            const url = categoryId ? 
                `{{ route('categories.update', ':id') }}`.replace(':id', categoryId) :
                '{{ route("categories.store") }}';
            const method = categoryId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }

        function deleteCategory(categoryId) {
            const category = findCategory(categoryId);
            if (!category) {
                Swal.fire('Error', 'Category not found', 'error');
                return;
            }
            
            const childrenCount = countTotalDescendants(category);
            const categoryName = category.title_translation?.title || 'Unknown';

            // Always show confirmation dialog
            if (childrenCount > 0) {
                Swal.fire({
                    title: 'Delete Category with Subcategories?',
                    html: `
                        <div class="text-start">
                            <p><strong>Category:</strong> ${categoryName}</p>
                            <p><strong>Warning:</strong> This category contains <strong>${childrenCount}</strong> subcategories.</p>
                            <p>All subcategories will also be deleted permanently.</p>
                            <p class="text-danger mt-3"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone!</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete everything!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        performDelete(categoryId);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Delete Category?',
                    html: `
                        <div class="text-start">
                            <p><strong>Category:</strong> ${categoryName}</p>
                            <p>Are you sure you want to delete this category?</p>
                            <p class="text-danger mt-3"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone!</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        performDelete(categoryId);
                    }
                });
            }
        }

        function performDelete(categoryId) {
            console.log('Attempting to delete category:', categoryId);
            
            fetch(`{{ route('categories.destroy', ':id') }}`.replace(':id', categoryId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                console.log('Delete response status:', response.status);
                return response.json();
            }).then(data => {
                console.log('Delete response data:', data);
                if (data.success) {
                    Swal.fire('Success', data.message, 'success');
                    location.reload();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }).catch(error => {
                console.error('Delete error:', error);
                Swal.fire('Error', 'An error occurred while deleting the category', 'error');
            });
        }

        function bulkDelete() {
            if (selectedCategories.size === 0) {
                Swal.fire('No Selection', 'Please select categories to delete first.', 'info');
                return;
            }

            // Get names of selected categories for display
            const selectedCategoryNames = Array.from(selectedCategories).map(id => {
                const category = findCategory(id);
                return category?.title_translation?.title || `ID: ${id}`;
            });

            Swal.fire({
                title: 'Bulk Delete Categories?',
                html: `
                    <div class="text-start">
                        <p><strong>Selected Categories (${selectedCategories.size}):</strong></p>
                        <ul class="list-unstyled">
                            ${selectedCategoryNames.map(name => `<li>• ${name}</li>`).join('')}
                        </ul>
                        <p class="text-danger mt-3">
                            <i class="fas fa-exclamation-triangle"></i> 
                            All selected categories and their subcategories will be deleted permanently!
                        </p>
                        <p class="text-danger"><strong>This action cannot be undone!</strong></p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Yes, delete ${selectedCategories.size} categories!`,
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                focusCancel: true,
                customClass: {
                    popup: 'swal-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("categories.bulkDelete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            category_ids: Array.from(selectedCategories)
                        })
                    }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Success', data.message, 'success');
                                location.reload();
                            } else {
                                if (data.requires_confirmation) {
                                    // Handle server-side confirmation for categories with children
                                    Swal.fire({
                                        title: 'Categories contain subcategories',
                                        text: data.message,
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, delete everything!',
                                        cancelButtonText: 'Cancel',
                                        confirmButtonColor: '#dc3545'
                                    }).then((confirmResult) => {
                                        if (confirmResult.isConfirmed) {
                                            // Force delete with confirmation
                                            fetch('{{ route("categories.bulkDelete") }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({
                                                    category_ids: Array.from(selectedCategories),
                                                    force: true
                                                })
                                            }).then(response => response.json())
                                                .then(forceData => {
                                                    if (forceData.success) {
                                                        Swal.fire('Success', forceData.message, 'success');
                                                        location.reload();
                                                    } else {
                                                        Swal.fire('Error', forceData.message, 'error');
                                                    }
                                                });
                                        }
                                    });
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Bulk delete error:', error);
                            Swal.fire('Error', 'An error occurred during bulk delete', 'error');
                        });
                }
            });
        }

        function bulkMove() {
            if (selectedCategories.size === 0) return;

            const modal = new bootstrap.Modal(document.getElementById('bulkMoveModal'));
            modal.show();
        }

        function confirmBulkMove() {
            const newParentId = document.getElementById('newParentCategory').value || null;

            fetch('{{ route("categories.bulkMove") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    category_ids: Array.from(selectedCategories),
                    new_parent_id: newParentId
                })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });

            bootstrap.Modal.getInstance(document.getElementById('bulkMoveModal')).hide();
        }

        function searchCategories() {
            const query = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const level = document.getElementById('levelFilter').value;

            const params = new URLSearchParams({
                query: query,
                status: status,
                level: level
            });

            fetch(`{{ route('categories.search') }}?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        categories = data.categories;
                        window.categories = categories; // Update global reference
                        renderCategories();
                        
                        // Only show success message for substantial searches, not live typing
                        if (query.length > 2 || status || level) {
                            console.log(`Search completed: ${data.categories.length} categories found`);
                        }
                    } else {
                        Swal.fire('Error', 'Search failed', 'error');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    Swal.fire('Error', 'Search failed', 'error');
                });
        }
        
        function clearAllFilters() {
            // Clear all input fields
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('levelFilter').value = '';
            document.getElementById('clearSearch').style.display = 'none';
            
            // Trigger search to show all categories
            searchCategories();
            
            // Focus search input
            document.getElementById('searchInput').focus();
            
            Swal.fire({
                icon: 'success',
                title: 'Filters Cleared',
                text: 'All search filters have been reset',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Auto-save expand state when page is unloaded
        window.addEventListener('beforeunload', saveExpandState);
        
        // Test function
        function testModal() {
            console.log('testModal called');
            alert('Test button clicked! JavaScript is working.');
            
            // Check if Bootstrap is loaded
            console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
            
            // Check if modal element exists
            const modalElement = document.getElementById('categoryModal');
            console.log('Modal element found:', modalElement ? 'Yes' : 'No');
            
            if (modalElement) {
                try {
                    // Try to open modal directly
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                    console.log('Modal opened successfully');
                } catch (error) {
                    console.error('Error opening modal:', error);
                    alert('Error opening modal: ' + error.message);
                }
            } else {
                alert('Modal element not found!');
            }
        }
        
        // Test if categories are loaded
        function debugCategories() {
            console.log('Global categories variable:', window.categories);
            console.log('Categories length:', window.categories?.length);
            return window.categories;
        }
        
        // Direct test for edit modal
        function testEditModal() {
            console.log('Testing edit modal directly...');
            
            // Get first category ID
            const firstCategory = categories[0];
            console.log('First category:', firstCategory);
            
            if (firstCategory) {
                console.log('Calling showEditModal with ID:', firstCategory.id);
                showEditModal(firstCategory.id);
            } else {
                console.log('No categories found!');
            }
        }
        
        // Function to list all category IDs for debugging
        function listAllCategoryIds() {
            const allIds = [];
            
            function collectIds(categories) {
                for (let category of categories) {
                    allIds.push({
                        id: category.id,
                        title: category.title_translation?.title || 'Untitled',
                        parent_id: category.parent_id
                    });
                    
                    if (category.children_recursive && category.children_recursive.length > 0) {
                        collectIds(category.children_recursive);
                    }
                }
            }
            
            collectIds(categories);
            console.log('All available category IDs:', allIds);
            return allIds;
        }
        
        // Direct test function for finding category
        function testFindCategory(id) {
            console.log('=== Testing findCategory for ID:', id, '===');
            const result = findCategory(id);
            console.log('Result:', result);
            return result;
        }
        
        // Make functions globally available for testing
        window.testEditModal = testEditModal;
        window.debugCategories = debugCategories;
        window.listAllCategoryIds = listAllCategoryIds;
        window.testFindCategory = testFindCategory;
        window.findCategory = findCategory;
    </script>
</body>
</html>
