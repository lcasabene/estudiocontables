<?php $pageTitle = 'Auditoría'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <i class="bi bi-shield-check"></i> Registro de Auditoría
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="audit-table" class="table table-hover" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>ID Entidad</th>
                        <th>IP</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
$(function() {
    $('#audit-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= tenant_url("api/auditoria") ?>',
        columns: [
            { data: 'id', width: '60px' },
            { data: 'created_at', render: d => d || '' },
            { data: 'nombre_completo', render: d => d || '<span class="text-muted">Sistema</span>' },
            { data: 'accion', render: d => '<span class="badge bg-info">' + d + '</span>' },
            { data: 'entidad' },
            { data: 'entidad_id', render: d => d ? '#' + d : '-' },
            { data: 'ip', render: d => '<span class="small text-muted">' + (d || '') + '</span>' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-AR.json'
        }
    });
});
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
