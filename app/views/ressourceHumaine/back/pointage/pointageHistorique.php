<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des pointages</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
</head>

<body>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>
<div id="app">
        <?= Flight::menuBackOffice() ?>
        <div id="main">
            <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                Historique des pointages
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>Employé</th>
                                        <th>Date</th>
                                        <th>Heure d'arrivée</th>
                                        <th>Heure de départ</th>
                                        <th>Durée</th>
                                        <th>Retard (min)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pointages) && is_array($pointages)): ?>
                                        <?php foreach ($pointages as $p): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(($p['nom'] ?? '') . ' ' . ($p['prenom'] ?? '')) ?></td>
                                                <td><?= !empty($p['date_pointage']) ? htmlspecialchars(date('d/m/Y', strtotime($p['date_pointage']))) : '' ?></td>
                                                <td><?= !empty($p['datetime_checkin']) ? htmlspecialchars(date('H:i:s', strtotime($p['datetime_checkin']))) : '' ?></td>
                                                <td><?= !empty($p['datetime_checkout']) ? htmlspecialchars(date('H:i:s', strtotime($p['datetime_checkout']))) : '' ?></td>
                                                <td><?= htmlspecialchars($p['duree_work'] ?? '') ?></td>
                                                <td>
                                                    <?php 
                                                        $duree_work = $p['duree_work'] ?? '';
                                                        $retard = (int)($p['retard_min'] ?? 0); 
                                                    ?>
                                                    <?php if ($duree_work === '00:00:00'): ?>
                                                        <span class="badge bg-secondary">Absent</span>
                                                    <?php else: ?>
                                                        <span class="badge <?= $retard > 0 ? 'bg-danger' : 'bg-success' ?>">
                                                            <?= $retard ?> min
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary edit-pointage" data-id="<?= htmlspecialchars($p['id_pointage']) ?>" data-checkin="<?= htmlspecialchars($p['datetime_checkin'] ?? '') ?>" data-checkout="<?= htmlspecialchars($p['datetime_checkout'] ?? '') ?>">Modifier</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">Aucun pointage trouvé.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            <!-- Modal d'édition unique pour checkin/checkout -->
            <div class="modal fade" id="editPointageModal" tabindex="-1" aria-labelledby="editPointageLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPointageLabel">Modifier pointage</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editPointageForm">
                            <div class="modal-body">
                                <input type="hidden" id="ep_id_pointage" name="id_pointage" value="">
                                <div class="mb-3">
                                    <label class="form-label">Arrivée (checkin)</label>
                                    <input type="datetime-local" id="ep_datetime_checkin" name="datetime_checkin" class="form-control" value="">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Départ (checkout)</label>
                                    <input type="datetime-local" id="ep_datetime_checkout" name="datetime_checkout" class="form-control" value="">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</body>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

    <script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/simple-datatables.js"></script>

    <script>
    (function($){
        // Helper to convert SQL DATETIME to an <input type="datetime-local"> value
        function toInputDatetime(sqlDt) {
            if (!sqlDt) return '';
            return sqlDt.replace(' ', 'T').slice(0,16);
        }

        // Helper to format SQL DATETIME to a HH:mm:ss display string
        function toDisplayTime(sqlDt) {
            if (!sqlDt) return '';
            try {
                // Using Date object is safer for parsing and formatting
                return new Date(sqlDt).toLocaleTimeString('fr-FR', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
            } catch(e) {
                // Fallback for older browsers or unexpected formats
                return sqlDt.split(' ')[1] || '';
            }
        }

        $(document).on('click', '.edit-pointage', function(){
            var id = $(this).data('id');
            var checkin = $(this).data('checkin') || '';
            var checkout = $(this).data('checkout') || '';
            // store the clicked button for later DOM update
            $('#editPointageModal').data('rowBtn', $(this));
            $('#ep_id_pointage').val(id);
            $('#ep_datetime_checkin').val(toInputDatetime(checkin));
            $('#ep_datetime_checkout').val(toInputDatetime(checkout));
            $('#editPointageModal').modal('show');
        });

        $('#editPointageForm').on('submit', function(e){
            e.preventDefault();
            var data = {
                id_pointage: $('#ep_id_pointage').val(),
                datetime_checkin: $('#ep_datetime_checkin').val(),
                datetime_checkout: $('#ep_datetime_checkout').val()
            };
            if (data.datetime_checkin) data.datetime_checkin = data.datetime_checkin.replace('T',' ') + (data.datetime_checkin.length===16?':00':'');
            if (data.datetime_checkout) data.datetime_checkout = data.datetime_checkout.replace('T',' ') + (data.datetime_checkout.length===16?':00':'');

            $.post('<?= Flight::base() ?>/pointage/update', data, function(resp){
                if (resp && resp.success) {
                    var updated = resp.updated || null;
                    var $btn = $('#editPointageModal').data('rowBtn');
                    if ($btn && updated) {
                        var $tr = $btn.closest('tr');
                        // Update table cells with correct indices
                        $tr.find('td').eq(2).text(toDisplayTime(updated.datetime_checkin));
                        $tr.find('td').eq(3).text(toDisplayTime(updated.datetime_checkout));
                        $tr.find('td').eq(4).text(updated.duree_work || '');
                        
                        // Update retard with a badge
                        var retardCell = $tr.find('td').eq(5);
                        if (updated.duree_work === '00:00:00') {
                            retardCell.html('<span class="badge bg-secondary">Absent</span>');
                        } else {
                            var retard = (updated.retard_min !== null && updated.retard_min !== undefined) ? parseInt(updated.retard_min, 10) : 0;
                            var badgeClass = retard > 0 ? 'bg-danger' : 'bg-success';
                            var badgeHtml = `<span class="badge ${badgeClass}">${retard} min</span>`;
                            retardCell.html(badgeHtml);
                        }

                        // Also update the button's data attributes for the next edit
                        $btn.data('checkin', updated.datetime_checkin || '');
                        $btn.data('checkout', updated.datetime_checkout || '');
                    }
                    $('#editPointageModal').modal('hide');
                } else {
                    alert((resp && resp.message) ? resp.message : 'Erreur lors de la mise à jour');
                }
            }, 'json').fail(function(){
                alert('Erreur lors de la requête');
            });
        });

    })(jQuery);
    </script>

</html>