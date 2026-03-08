<?php $pageTitle = 'Clientes'; ob_start(); ?>

<style>
    .filter-bar {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        padding: .75rem 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .filter-bar label { font-size: .8rem; font-weight: 600; color: #475569; white-space: nowrap; }
    .filter-bar select { max-width: 250px; }
    .dt-buttons .btn { font-size: .8rem; }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> Listado de Clientes</span>
        <a href="<?= tenant_url('clientes/crear') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Cliente
        </a>
    </div>
    <div class="card-body">
        <!-- Filter bar -->
        <div class="filter-bar">
            <label><i class="bi bi-funnel"></i> Condición Fiscal:</label>
            <select id="filterCondicion" class="form-select form-select-sm">
                <option value="">Todas</option>
                <?php foreach ($condiciones as $cf): ?>
                <option value="<?= $cf['id'] ?>"><?= e($cf['nombre']) ?></option>
                <?php endforeach; ?>
                <option value="__none__">Sin asignar</option>
            </select>
            <div class="vr d-none d-md-block"></div>
            <small class="text-muted" id="filterInfo"></small>
        </div>

        <div class="table-responsive">
            <table id="clientes-table" class="table table-hover" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Razón Social</th>
                        <th>CUIT</th>
                        <th>Email</th>
                        <th>Condición Fiscal</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
$(function() {
    let condicionFilter = '';

    let table = $('#clientes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= tenant_url("api/clientes") ?>',
            data: function(d) {
                d.condicion_fiscal = condicionFilter;
            }
        },
        columns: [
            { data: 'id', width: '50px' },
            { data: 'razon_social' },
            { data: 'cuit' },
            { data: 'email', render: d => d || '<span class="text-muted">-</span>' },
            {
                data: 'condicion_fiscal',
                render: function(d) {
                    if (!d) return '<span class="text-muted">Sin asignar</span>';
                    let colors = {
                        'Monotributista': 'success',
                        'Responsable Inscripto': 'primary',
                        'Exento': 'warning',
                        'Consumidor Final': 'secondary'
                    };
                    let color = colors[d] || 'info';
                    return `<span class="badge bg-${color}">${d}</span>`;
                }
            },
            {
                data: 'telefono',
                render: d => d || '<span class="text-muted">-</span>',
                visible: false
            },
            {
                data: 'direccion',
                render: d => d || '<span class="text-muted">-</span>',
                visible: false
            },
            {
                data: 'id',
                orderable: false,
                render: function(id) {
                    let base = '<?= tenant_url("clientes") ?>';
                    return `
                        <div class="btn-group btn-group-sm">
                            <a href="${base}/${id}/ver" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="${base}/${id}/editar" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="${base}/${id}/claves" class="btn btn-outline-danger" title="Claves"><i class="bi bi-key"></i></a>
                            <a href="${base}/${id}/documentos" class="btn btn-outline-success" title="Documentos"><i class="bi bi-folder"></i></a>
                        </div>`;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-AR.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        dom: "<'row align-items-center'<'col-md-6'B><'col-md-6'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
            {
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                className: 'btn btn-sm btn-success',
                action: function() { exportToFile('excel'); }
            },
            {
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                className: 'btn btn-sm btn-danger',
                action: function() { exportToFile('pdf'); }
            },
            {
                text: '<i class="bi bi-printer"></i> Imprimir',
                className: 'btn btn-sm btn-outline-secondary',
                action: function() { exportToFile('print'); }
            },
            {
                text: '<i class="bi bi-file-earmark-excel text-success"></i> Monotributistas',
                className: 'btn btn-sm btn-outline-success',
                action: function() {
                    let monotribId = getCondicionId('Monotributista');
                    if (!monotribId) {
                        alert('No se encontró la condición "Monotributista"');
                        return;
                    }
                    exportToFile('excel', monotribId, 'Monotributistas');
                }
            }
        ],
        drawCallback: function(settings) {
            let info = settings.json;
            if (info) {
                let filterName = $('#filterCondicion option:selected').text();
                let text = condicionFilter
                    ? `Mostrando ${info.recordsFiltered} de ${info.recordsTotal} clientes (${filterName})`
                    : `${info.recordsTotal} clientes en total`;
                $('#filterInfo').text(text);
            }
        }
    });

    // Filter by condicion fiscal
    $('#filterCondicion').on('change', function() {
        condicionFilter = $(this).val();
        table.ajax.reload();
    });

    // Helper to find condicion ID by name
    function getCondicionId(name) {
        let found = null;
        $('#filterCondicion option').each(function() {
            if ($(this).text().toLowerCase().includes(name.toLowerCase()) && $(this).val() !== '' && $(this).val() !== '__none__') {
                found = $(this).val();
                return false;
            }
        });
        return found;
    }

    // ==========================================
    // EXPORT: fetch ALL data from API, then generate file client-side
    // ==========================================
    function exportToFile(type, overrideCf, label) {
        let cf = overrideCf || condicionFilter;
        let searchVal = table.search() || '';
        let filterLabel = label || ($('#filterCondicion option:selected').text()) || 'Todos';
        if (!cf && !label) filterLabel = 'Todos';

        let url = '<?= tenant_url("api/clientes/export") ?>?condicion_fiscal=' + encodeURIComponent(cf) + '&search=' + encodeURIComponent(searchVal);

        $.get(url).done(function(resp) {
            let rows = resp.data;
            if (!rows || rows.length === 0) {
                alert('No hay datos para exportar.');
                return;
            }

            if (type === 'excel') {
                generateExcel(rows, filterLabel);
            } else if (type === 'pdf') {
                generatePdf(rows, filterLabel);
            } else if (type === 'print') {
                generatePrint(rows, filterLabel);
            }
        }).fail(function() {
            alert('Error al obtener datos para exportar.');
        });
    }

    function generateExcel(rows, label) {
        let headers = ['ID', 'Razón Social', 'CUIT', 'Email', 'Condición Fiscal', 'Teléfono', 'Dirección'];
        let csvRows = [headers.join('\t')];
        rows.forEach(function(r) {
            csvRows.push([
                r.id, r.razon_social, r.cuit, r.email || '', r.condicion_fiscal || 'Sin asignar',
                r.telefono || '', r.direccion || ''
            ].join('\t'));
        });
        let bom = '\uFEFF';
        let blob = new Blob([bom + csvRows.join('\n')], { type: 'application/vnd.ms-excel;charset=utf-8' });
        let filename = label.toLowerCase().replace(/\s+/g, '_') + '_<?= date("Y-m-d") ?>.xls';
        downloadBlob(blob, filename);
    }

    function generatePdf(rows, label) {
        let body = [['ID', 'Razón Social', 'CUIT', 'Email', 'Cond. Fiscal']];
        rows.forEach(function(r) {
            body.push([
                r.id.toString(), r.razon_social, r.cuit,
                r.email || '-', r.condicion_fiscal || 'Sin asignar'
            ]);
        });
        let docDef = {
            pageOrientation: 'landscape',
            content: [
                { text: label + ' - <?= e(\Core\Tenant::name() ?? "Estudio") ?>', style: 'header' },
                { text: 'Generado: <?= date("d/m/Y H:i") ?> | Total: ' + rows.length + ' registros\n\n', style: 'sub' },
                {
                    table: {
                        headerRows: 1,
                        widths: ['6%', '34%', '16%', '22%', '22%'],
                        body: body
                    },
                    layout: {
                        fillColor: function(i) { return i === 0 ? '#2563eb' : (i % 2 === 0 ? '#f8fafc' : null); }
                    }
                }
            ],
            styles: {
                header: { fontSize: 16, bold: true, margin: [0, 0, 0, 5] },
                sub: { fontSize: 9, color: '#666' }
            },
            defaultStyle: { fontSize: 8 }
        };
        // Color header text white
        docDef.content[2].table.body[0] = docDef.content[2].table.body[0].map(h => ({ text: h, color: '#fff', bold: true }));
        pdfMake.createPdf(docDef).download(label.toLowerCase().replace(/\s+/g, '_') + '_<?= date("Y-m-d") ?>.pdf');
    }

    function generatePrint(rows, label) {
        let html = `<html><head><title>${label}</title>
            <style>body{font-family:Arial,sans-serif;font-size:12px;margin:20px}
            h2{color:#1e293b}table{width:100%;border-collapse:collapse;margin-top:10px}
            th{background:#2563eb;color:#fff;padding:8px;text-align:left;font-size:11px}
            td{border-bottom:1px solid #e2e8f0;padding:6px 8px;font-size:11px}
            tr:nth-child(even){background:#f8fafc}.meta{color:#666;font-size:10px}</style></head><body>
            <h2>${label} - <?= e(\Core\Tenant::name() ?? "Estudio") ?></h2>
            <p class="meta">Fecha: <?= date("d/m/Y H:i") ?> | Total: ${rows.length} registros</p>
            <table><thead><tr><th>ID</th><th>Razón Social</th><th>CUIT</th><th>Email</th><th>Cond. Fiscal</th><th>Teléfono</th></tr></thead><tbody>`;
        rows.forEach(function(r) {
            html += `<tr><td>${r.id}</td><td>${esc(r.razon_social)}</td><td>${esc(r.cuit)}</td>
                <td>${esc(r.email||'-')}</td><td>${esc(r.condicion_fiscal||'Sin asignar')}</td><td>${esc(r.telefono||'-')}</td></tr>`;
        });
        html += '</tbody></table></body></html>';
        let w = window.open('', '_blank');
        w.document.write(html);
        w.document.close();
        w.print();
    }

    function downloadBlob(blob, filename) {
        let a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
    }

    function esc(s) {
        if (!s) return '';
        let d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
});
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
