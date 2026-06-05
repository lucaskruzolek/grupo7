<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Venta #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            background: #fff;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #2a9d8f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-section h1 {
            color: #2a9d8f;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .logo-section p {
            margin: 5px 0 0 0;
            color: #777;
            font-size: 12px;
        }
        .details-section {
            text-align: right;
        }
        .details-section h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .details-section p {
            margin: 5px 0 0 0;
            color: #555;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }
        .info-block h3 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
            color: #555;
            text-transform: uppercase;
        }
        .info-block p {
            margin: 5px 0;
            color: #333;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .table-items th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #555;
        }
        .table-items td {
            border-bottom: 1px solid #eee;
            padding: 12px 10px;
        }
        .table-items tr:last-child td {
            border-bottom: none;
        }
        .total-box {
            display: flex;
            justify-content: flex-end;
        }
        .total-table {
            width: 250px;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 8px 10px;
        }
        .total-table tr.grand-total td {
            border-top: 2px solid #2a9d8f;
            font-weight: 700;
            font-size: 18px;
            color: #2a9d8f;
        }
        .print-actions {
            max-width: 800px;
            margin: 20px auto;
            text-align: right;
        }
        .btn-print {
            background-color: #2a9d8f;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .btn-print:hover {
            background-color: #238276;
        }

        @media print {
            .print-actions {
                display: none;
            }
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">Imprimir Factura 🖨️</button>
    </div>

    <div class="invoice-box">
        <div class="invoice-header">
            <div class="logo-section">
                <h1>Pet Threads</h1>
                <p>Indumentaria Exclusiva para Mascotas</p>
            </div>
            <div class="details-section">
                <h2>Comprobante de Pedido</h2>
                <p><strong>Nro:</strong> #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Fecha:</strong> {{ $venta->fecha_venta ? $venta->fecha_venta->format('d/m/Y H:i') : $venta->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <h3>Cliente</h3>
                <p><strong>Nombre:</strong> {{ $venta->usuario ? $venta->usuario->nombre . ' ' . $venta->usuario->apellido : 'Invitado' }}</p>
                <p><strong>Email:</strong> {{ $venta->usuario ? $venta->usuario->email : 'N/A' }}</p>
            </div>
            <div class="info-block" style="text-align: right;">
                <h3>Pago y Despacho</h3>
                <p><strong>Método de Pago:</strong> {{ $venta->formaPago ? $venta->formaPago->descripcion : 'No especificado' }}</p>
                <p><strong>Estado del Pedido:</strong> {{ $venta->estado }}</p>
            </div>
        </div>

        <table class="table-items">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="text-align: center;">Talle</th>
                    <th style="text-align: center;">Color</th>
                    <th style="text-align: center;">Cant.</th>
                    <th style="text-align: right;">Precio Unit.</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td style="text-align: center;">{{ $detalle->producto->talle }}</td>
                        <td style="text-align: center;">{{ $detalle->producto->color ? $detalle->producto->color->nombre : 'N/A' }}</td>
                        <td style="text-align: center;">{{ $detalle->cantidad }}</td>
                        <td style="text-align: right;">${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                        <td style="text-align: right;">${{ number_format($detalle->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <table class="total-table">
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;">${{ number_format($venta->total, 2, ',', '.') }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total:</td>
                    <td style="text-align: right;">${{ number_format($venta->total, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
