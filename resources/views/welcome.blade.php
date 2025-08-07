<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .category-item {
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .category-header {
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
        }
        .drag-handle {
            cursor: grab;
        }
        .category-children {
            padding-right: 50px !important;
        }
        .collapse-btn {
            background: none;
            border: none;
            font-size: 1rem;
            color: #555;
            margin-left: 10px;
        }
        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between mb-3">
            <h4 class="mb-0 text-dark">
                <i class="fas fa-sitemap text-primary me-2"></i> إدارة التصنيفات
            </h4>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="expandAll()">
                    <i class="fas fa-expand"></i> عرض الكل
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                    <i class="fas fa-compress"></i> إخفاء الكل
                </button>
            </div>
        </div>
        <div >
            <div  id="categoryList" data-parent-id=""></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let categories = @json($cats);

        document.addEventListener('DOMContentLoaded', function () {
            renderCategories();
            initializeSortables();
        });

        function renderCategories() {
            const container = document.getElementById('categoryList');
            container.innerHTML = '';
            categories.forEach(cat => {
                container.appendChild(buildCategory(cat));
            });
        }

        function buildCategory(cat, level = 0) {
            const card = document.createElement('div');
            card.classList.add('card', 'custom-card', 'mb-2');
            card.dataset.id = cat.id;

            const collapseId = `collapse-${cat.id}`;
            const hasChildren = cat.children_recursive && cat.children_recursive.length > 0;

            // Header (Parent Category)
            const header = document.createElement('div');
            header.className = 'card-header d-block';
            header.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fas fa-grip-vertical drag-handle me-2"></i>
                ${hasChildren ? `<button class="collapse-btn" data-bs-toggle="collapse" data-bs-target="#${collapseId}"><i class="fas fa-chevron-down"></i></button>` : ''}
                <div>
                    (<span>${cat.id} - ${cat.ord}</span> )<div class="fw-bold">${cat.title_translation?.title || '-'}</div>
                    <small class="text-muted">By ${cat.user?.name || 'N/A'} - ${cat.children_recursive.length} child(ren)</small>
                </div>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-primary btn-action" title="Edit"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-outline-success btn-action" title="Add Child"><i class="fas fa-plus"></i></button>
                <button class="btn btn-sm btn-outline-danger btn-action" title="Delete"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    `;
            card.appendChild(header);

            // Children (in body)
            if (hasChildren) {
                const body = document.createElement('div');
                body.className = 'collapse show card-body category-children';
                body.id = collapseId;
                body.dataset.parentId = cat.id;

                cat.children_recursive.forEach(child => {
                    body.appendChild(buildCategory(child, level + 1));
                });

                card.appendChild(body);
            }

            return card;
        }


        function initializeSortables() {
            new Sortable(document.getElementById('categoryList'), {
                group: 'categories',
                handle: '.drag-handle',
                animation: 150,
                onEnd: updateOrderAjax
            });

            document.querySelectorAll('.category-children').forEach(container => {
                new Sortable(container, {
                    group: 'categories',
                    handle: '.drag-handle',
                    animation: 150,
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

            const params = new URLSearchParams(data).toString();

            fetch(`{{ route('cat.updateOrder') }}?${params}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }).then(res => res.json())
                .then(response => console.log(response));
        }

        function expandAll() {
            document.querySelectorAll('.collapse').forEach(c => bootstrap.Collapse.getOrCreateInstance(c).show());
        }

        function collapseAll() {
            document.querySelectorAll('.collapse').forEach(c => bootstrap.Collapse.getOrCreateInstance(c).hide());
        }
    </script>
</body>
</html>
