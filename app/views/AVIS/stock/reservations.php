<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations de stock</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .table-responsive { max-height: 55vh; }
        .form-inline .form-control { min-width: 120px; }
    </style>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
</head>
<body>
<div id="app">
    <?php Flight::render('ui/menu/avis/stock/menuStock'); ?>
    <div id="main" class="layout-navbar">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div id="main-content">
            <div class="page-heading">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>Réservations</h3>
                        <p class="text-muted">Créer, annuler et lister les réservations de stock.</p>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Filtres</h5>
                            </div>
                            <div class="card-body">
                                <form id="filters" class="row g-2 align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label">Article (ID)</label>
                                        <input type="number" class="form-control" id="flt-article" placeholder="ex: 101">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Dépôt (ID)</label>
                                        <input type="number" class="form-control" id="flt-depot" placeholder="ex: 1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Référence</label>
                                        <input type="text" class="form-control" id="flt-reference" placeholder="ex: BL#123">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" id="btn-apply">Appliquer</button>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <button type="button" class="btn btn-outline-secondary" id="btn-reset">Réinitialiser</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title">Nouvelle réservation</h5></div>
                            <div class="card-body">
                                <form id="form-reserve" class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Article</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="reserve-article-search" placeholder="Recherche article (code/désignation)">
                                            <button class="btn btn-outline-secondary" type="button" id="reserve-article-search-btn">Rechercher</button>
                                        </div>
                                        <select class="form-select mt-1" id="reserve-article" required></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Dépôt</label>
                                        <select class="form-select" id="reserve-depot" required></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Quantité</label>
                                        <input type="number" step="0.01" class="form-control" id="reserve-quantite" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Référence (optionnel)</label>
                                        <input type="text" class="form-control" id="reserve-reference" placeholder="table#id">
                                    </div>
                                    <div class="col-12 mt-2">
                                        <button class="btn btn-success" type="submit">Réserver</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title">Réservations en cours</h5>
                                <span class="text-muted small" id="count-label"></span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="reservations-table">
                                        <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Dépôt</th>
                                            <th>Référence</th>
                                            <th class="text-end">Réservé</th>
                                            <th class="text-end">Dernière MAJ</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>
            </div>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>

<script>
(function(){
    let typesMap = {}; // code -> id_type

    function toast(msg, type='info'){
        Toastify({ text: msg, duration: 3000, gravity: 'top', position: 'right', style: { background: type==='error' ? '#dc3545' : (type==='success' ? '#198754' : '#0d6efd') } }).showToast();
    }

    function loadTypes(){
        return $.get('<?= Flight::base() ?>/api/stock/types').then(function(res){
            if(res.success){ (res.data||[]).forEach(function(t){ typesMap[(t.code||'').toUpperCase()] = t.id_type_mouvement_stock; }); }
        });
    }

    function loadDepots(){
        return $.get('<?= Flight::base() ?>/api/stock/depots').then(function(res){
            if(res.success){
                const $sel = $('#reserve-depot');
                $sel.empty();
                (res.data||[]).forEach(function(d){
                    $sel.append('<option value="'+d.id_depot+'">'+(d.nom||d.code||d.id_depot)+'</option>');
                });
            }
        });
    }

    function searchArticles(){
        const q = $('#reserve-article-search').val();
        return $.get('<?= Flight::base() ?>/api/stock/articles', { q:q }).then(function(res){
            const $sel = $('#reserve-article');
            $sel.empty();
            if(res.success){
                (res.data||[]).forEach(function(a){
                    $sel.append('<option value="'+a.id_article+'">'+(a.code? (a.code+' - ') : '')+(a.designation||a.id_article)+'</option>');
                });
            }
        });
    }

    function applyFilters(){
        const a = $('#flt-article').val();
        const d = $('#flt-depot').val();
        const r = $('#flt-reference').val();
        const params = $.param({ article: a||undefined, depot: d||undefined, reference: r||undefined });
        $('#reservations-table tbody').html('<tr><td colspan="6">Chargement...</td></tr>');
        $.get('<?= Flight::base() ?>/api/stock/reservations?' + params).done(function(res){
            if(!res.success){ toast(res.message||'Erreur', 'error'); return; }
            const rows = res.data||[];
            $('#count-label').text(rows.length + ' éléments');
            const html = rows.map(function(x){
                const art = x.article_designation || x.id_article;
                const dep = x.depot_nom || x.id_depot;
                const ref = x.reference || '';
                const qty = parseFloat(x.reserved||0).toFixed(2);
                const last = x.last_date || '';
                return '<tr data-article="'+x.id_article+'" data-depot="'+x.id_depot+'" data-reference="'+ref+'" data-reserved="'+qty+'">' +
                    '<td>'+art+'</td>'+
                    '<td>'+dep+'</td>'+
                    '<td>'+ref+'</td>'+
                    '<td class="text-end">'+qty+'</td>'+
                    '<td class="text-end">'+last+'</td>'+
                    '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-cancel-row">Annuler</button></td>'+
                '</tr>';
            }).join('');
            $('#reservations-table tbody').html(html || '<tr><td colspan="6" class="text-center text-muted">Aucune réservation en cours</td></tr>');
        }).fail(function(){ toast('Chargement impossible', 'error'); });
    }

    $('#btn-apply').on('click', applyFilters);
    $('#btn-reset').on('click', function(){ $('#flt-article,#flt-depot,#flt-reference').val(''); applyFilters(); });

    $('#reserve-article-search-btn').on('click', searchArticles);
    $('#reserve-article-search').on('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); searchArticles(); }});

    $('#form-reserve').on('submit', function(ev){ ev.preventDefault();
        const payload = {
            id_article: parseInt($('#reserve-article').val()),
            id_depot: parseInt($('#reserve-depot').val()),
            id_type_mouvement_stock: typesMap['RESERVATION'],
            sens: 2, // sortie (catégorie)
            quantite: parseFloat($('#reserve-quantite').val()),
            table_reference: (function(){ const v = $('#reserve-reference').val(); return v && v.indexOf('#')>0 ? v.split('#')[0] : null; })(),
            id_reference: (function(){ const v = $('#reserve-reference').val(); return v && v.indexOf('#')>0 ? parseInt(v.split('#')[1]) : null; })()
        };
        $.post('<?= Flight::base() ?>/api/stock/mouvements/create', payload).done(function(res){
            if(res.success){ toast('Réservation enregistrée', 'success'); applyFilters(); }
            else { toast(res.message||'Erreur', 'error'); }
        }).fail(function(){ toast('Envoi impossible', 'error'); });
    });

    $('#reservations-table').on('click', '.btn-cancel-row', function(){
        const $tr = $(this).closest('tr');
        const idArticle = parseInt($tr.data('article'));
        const idDepot = parseInt($tr.data('depot'));
        const reference = $tr.data('reference')||null;
        const reserved = parseFloat($tr.data('reserved')||0);
        const qty = prompt('Quantité à annuler (max '+reserved.toFixed(2)+')', reserved.toFixed(2));
        if(!qty) return;
        const qf = parseFloat(qty);
        if(isNaN(qf) || qf<=0) { toast('Quantité invalide', 'error'); return; }
        const payload = {
            id_article: idArticle,
            id_depot: idDepot,
            id_type_mouvement_stock: typesMap['ANNULATION_RESERVATION'],
            sens: 2,
            quantite: qf,
            table_reference: (function(){ return reference && reference.indexOf('#')>0 ? reference.split('#')[0] : null; })(),
            id_reference: (function(){ return reference && reference.indexOf('#')>0 ? parseInt(reference.split('#')[1]) : null; })()
        };
        $.post('<?= Flight::base() ?>/api/stock/mouvements/create', payload).done(function(res){
            if(res.success){ toast('Annulation enregistrée', 'success'); applyFilters(); }
            else { toast(res.message||'Erreur', 'error'); }
        }).fail(function(){ toast('Envoi impossible', 'error'); });
    });

    // init
    $.when(loadTypes(), loadDepots()).then(function(){ searchArticles().then(applyFilters); });
})();
</script>
</body>
</html>