<?php
$dias_mes = 31;

$_vendedores = array();

if($vendedores){
    foreach ($vendedores as $row_vendedor) {
        $_vendedores_contado[$row_vendedor->id_vendedor] = $row_vendedor->vendedor; 
        $_vendedores_ctacte[$row_vendedor->id_vendedor] = $row_vendedor->vendedor; 
    }
}

for ($i=1; $i <= $dias_mes; $i++) { 
	$mes[$i]		= 0;
	$contado[$i]	= 0;
	$ctacte[$i]		= 0;
	$remito[$i]		= 0;
	$devolucion[$i]	= 0;
	$anulacion[$i]	= 0;
    
    foreach ($_vendedores_contado as $v_key => $v_value) {
        $_vendedores_contado[$v_key][$i] = 0;
    }
    foreach ($_vendedores_ctacte as $v_key => $v_value) {
        $_vendedores_ctacte[$v_key][$i] = 0;
    }
}

$cant_ctacte = 0;
$cant_contado = 0;
$cant_devoluciones = 0;
$cant_anulaciones = 0;

if($presupuestos)
{
	foreach ($presupuestos as $row) 
	{
		$mes_fecha = date('d', strtotime($row->fecha));
		
		if($mes_fecha == '01'){ $mes_fecha = 1; }
		if($mes_fecha == '02'){ $mes_fecha = 2; }
		if($mes_fecha == '03'){ $mes_fecha = 3; }
		if($mes_fecha == '04'){ $mes_fecha = 4; }
		if($mes_fecha == '05'){ $mes_fecha = 5; }
		if($mes_fecha == '06'){ $mes_fecha = 6; }
		if($mes_fecha == '07'){ $mes_fecha = 7; }
		if($mes_fecha == '08'){ $mes_fecha = 8; }
		if($mes_fecha == '09'){ $mes_fecha = 9; }
		
		$mes[$mes_fecha] = $mes[$mes_fecha] + $row->monto;
		if($row->tipo == 2)
		{
            $ctacte[$mes_fecha] = $ctacte[$mes_fecha] + $row->monto;
            $_vendedores_ctacte[$row->id_vendedor][$mes_fecha] = $_vendedores_ctacte[$row->id_vendedor][$mes_fecha] + $row->monto;
			$cant_ctacte = $cant_ctacte + 1; 
        } else 
        {
			$contado[$mes_fecha] = $contado[$mes_fecha] + $row->monto;
            $_vendedores_contado[$row->id_vendedor][$mes_fecha] = $_vendedores_contado[$row->id_vendedor][$mes_fecha] + $row->monto;
			$cant_contado = $cant_contado + 1;
		}
	}	
}

if($remitos)
{
	foreach ($remitos as $row)
	{
		$mes_fecha = date('d', strtotime($row->fecha));
		
		if($mes_fecha == '01'){ $mes_fecha = 1; }
		if($mes_fecha == '02'){ $mes_fecha = 2; }
		if($mes_fecha == '03'){ $mes_fecha = 3; }
		if($mes_fecha == '04'){ $mes_fecha = 4; }
		if($mes_fecha == '05'){ $mes_fecha = 5; }
		if($mes_fecha == '06'){ $mes_fecha = 6; }
		if($mes_fecha == '07'){ $mes_fecha = 7; }
		if($mes_fecha == '08'){ $mes_fecha = 8; }
		if($mes_fecha == '09'){ $mes_fecha = 9; }
		
		$remito[$mes_fecha] = $remito[$mes_fecha] + $row->monto;
	}		
}

if($devoluciones)
{
	foreach ($devoluciones as $row)
	{
		$mes_fecha = date('d', strtotime($row->fecha));
		
		if($mes_fecha == '01'){ $mes_fecha = 1; }
		if($mes_fecha == '02'){ $mes_fecha = 2; }
		if($mes_fecha == '03'){ $mes_fecha = 3; }
		if($mes_fecha == '04'){ $mes_fecha = 4; }
		if($mes_fecha == '05'){ $mes_fecha = 5; }
		if($mes_fecha == '06'){ $mes_fecha = 6; }
		if($mes_fecha == '07'){ $mes_fecha = 7; }
		if($mes_fecha == '08'){ $mes_fecha = 8; }
		if($mes_fecha == '09'){ $mes_fecha = 9; }
		
		$devolucion[$mes_fecha] = $devolucion[$mes_fecha] + $row->monto;
		
		$cant_devoluciones = $cant_devoluciones + 1;
	}		
}

if($anulaciones)
{
	foreach ($anulaciones as $row)
	{
		$mes_fecha = date('d', strtotime($row->fecha));
		
		if($mes_fecha == '01'){ $mes_fecha = 1; }
		if($mes_fecha == '02'){ $mes_fecha = 2; }
		if($mes_fecha == '03'){ $mes_fecha = 3; }
		if($mes_fecha == '04'){ $mes_fecha = 4; }
		if($mes_fecha == '05'){ $mes_fecha = 5; }
		if($mes_fecha == '06'){ $mes_fecha = 6; }
		if($mes_fecha == '07'){ $mes_fecha = 7; }
		if($mes_fecha == '08'){ $mes_fecha = 8; }
		if($mes_fecha == '09'){ $mes_fecha = 9; }
		
		$anulacion[$mes_fecha] = $anulacion[$mes_fecha] + $row->monto;
		
		$cant_anulaciones = $cant_anulaciones + 1;
	}		
}

?>
<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Sumas totales de los montos de los presupuestos
				</div>
				
				<div class="panel-body">
					<div id="grafico" style="min-width: 310px; height: 400px; margin-bottom: 35px;"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Cantidad de ventas
				</div>
				
				<div class="panel-body">
					<div id="torta" style="min-width: 310px; height: 405px; max-width: 600px; margin: 0 auto"></div>
				</div>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<form method="post" action="<?php echo base_url().'index.php/estadisticas/verMas'?>">
						<input name="inicio" type="hidden" value="<?php echo $inicio;?>" />
						<input name="fin" type="hidden" value="<?php echo $fin;?>" />
						Top 10 de los artículos más vendidos 
						<button type="submit" class="btn btn-succes btn-xs">Ver mas</button>	
					</form>
				</div>
				
				<div class="panel-body">
					<table class="table table-hover" style="font-size: 13px">
						<thead>
							<tr>
								<th>Cant</th>
								<th>Descripción</th>
							</tr>
						</thead>
						<tbody>
					<?php
					if($articulos)
					{
						foreach ($articulos as $row)
						{
							echo "<tr>";
							echo "<td>".$row->cantidad."</td>";
							echo "<td><a title='Ver detalle' href='".base_url()."index.php/articulos/articulo_abm/read/".$row->id_articulo."'>";
							echo $row->descripcion."</a></td>";
							echo "</tr>";
						} 
					}
					?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>		
				
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Sumas totales de los montos de los presupuestos por tipo
				</div>
				
				<div class="panel-body">
					<div id="grafico2" style="min-width: 310px; height: 400px; margin-bottom: 35px;"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-danger">
				<div class="panel-heading">
					Sumas totales de los montos de las anulaciones y devoluciones
				</div>
				
				<div class="panel-body">
					<div id="grafico3" style="min-width: 310px; height: 400px; margin-bottom: 35px;"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-body">
					<form method="post">
						<div class="col-md-4">
							<label>Cambiar de periodo de tiempo</label>
						</div>
						<div class="col-md-2">
						<select class="form-control" name="mes"/>
							<option value="1"/>ene</option>
							<option value="2"/>feb</option>
							<option value="3"/>mar</option>
							<option value="4"/>abr</option>
							<option value="5"/>may</option>
							<option value="6"/>jun</option>
							<option value="7"/>jul</option>
							<option value="8"/>ago</option>
							<option value="9"/>sep</option>
							<option value="10"/>oct</option>
							<option value="11"/>nov</option>
							<option value="12"/>dic</option>
						</select>
						</div>
						<div class="col-md-2">
						<select class="form-control" name="ano"/>
							<?php 
							$comienzo = 2014;
							for ($i=0; $i < 10 ; $i++) { 
							$comienzo = $comienzo + 1;
							?> 
								<option value="<?php echo $comienzo?>"/><?php echo $comienzo?></option>
							<?php } ?>
						</select>
						</div>
						<div class="col-md-4">
							<button class="btn btn-default form-control" name="buscar">
								Cambiar
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	</div>


 <?php
            $cadena_contado = "";
            $cadena_ctacte = "";
            if($vendedores){
                foreach ($vendedores as $row_vendedor) {
                    $cadena_contado .= " { name: '".$row_vendedor->vendedor."',";
                    $cadena_contado .= "data: [";   
                    
                    $cadena_ctacte .= " { name: '".$row_vendedor->vendedor."',";
                    $cadena_ctacte .= "data: [";    
                    
                    foreach ($mes as $key => $value) {
                        $cadena_contado .= $_vendedores_contado[$row_vendedor->id_vendedor][$key].",";
                        $cadena_ctacte  .= $_vendedores_ctacte[$row_vendedor->id_vendedor][$key].",";
                    }   
                    
                    $cadena_contado .= "]";  
                    $cadena_contado .= "},"; 
                    
                    $cadena_ctacte .= "]";  
                    $cadena_ctacte .= "},";     
                }
            } 
            
            echo $cadena_contado;   
            echo $cadena_ctacte;   
            ?>

<script type="text/javascript">
$(function () {
    $('#grafico').highcharts({
    	title: {
            text: 'Sumas',
            x: -20 //center
        },
        subtitle: {
            text: 'Periodo : <?php echo $mes_actual."-".$ano_actual?>',
            x: -20
        },
        xAxis: {
            categories: [
            <?php
            foreach ($mes as $key => $value) {
				echo   "'".$key."',";
            }
			?>
            ]
        },
        yAxis: {
            title: {
                text: 'Cantidad'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ' $'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'Suma de montos',
            data: [
            <?php 
            foreach ($mes as $key => $value)
			{
				echo $value.",";
            }
			?>
			]
        }]
    });
    
    
    
    $('#grafico2').highcharts({
    	chart: {
            //type: 'bar'
            type: 'column'
        },
        title: {
            text: 'Sumas',
            x: -20 //center
        },
        subtitle: {
            text: 'Periodo : <?php echo $mes_actual."-".$ano_actual?>',
            x: -20
        },
        xAxis: {
            categories: [
            <?php
            foreach ($mes as $key => $value) {
				echo   "'".$key."',";
            }
			?>
            ]
        },
        yAxis: {
            title: {
                text: 'Cantidad'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ' $'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [
        	{
	            name: 'Contado',
	            data: [
		            <?php 
		            foreach ($contado as $key => $value)
					{
						echo   $value.",";
		            }
					?>
				]
			},
			{
	            name: 'Cuenta Corriente',
	            data: [
		            <?php 
		            foreach ($ctacte as $key => $value)
					{
						echo   $value.",";
		            }
					?>
				]
			},
			{
	            name: 'Remito',
	            data: [
		            <?php 
		            foreach ($remito as $key => $value)
					{
						echo   $value.",";
		            }
					?>
				]
			}
		]
    });
    
    
    
    
    $('#grafico3').highcharts({
    	chart: {
            //type: 'bar'
            type: 'column'
        },
        title: {
            text: 'Restas',
            x: -20 //center
        },
        subtitle: {
            text: 'Periodo : <?php echo $mes_actual."-".$ano_actual?>',
            x: -20
        },
        xAxis: {
            categories: [
            <?php
            foreach ($mes as $key => $value) {
				echo   "'".$key."',";
            }
			?>
            ]
        },
        yAxis: {
            title: {
                text: 'Cantidad'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ' $'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [
        	{
	            name: 'Anulaciones',
	            data: [
		            <?php 
		            foreach ($anulacion as $key => $value)
					{
						echo   $value.",";
		            }
					?>
				]
			},
			{
	            name: 'Devoluciones',
	            data: [
		            <?php 
		            foreach ($devolucion as $key => $value)
					{
						echo   $value.",";
		            }
					?>
				]
			}
		]
    });
    
    
    
    $('#torta').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Cantidad de ventas <?php echo $mes_actual."-".$ano_actual?>'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Cantidad de ventas',
            data: [
                ['Contado',   <?php echo $cant_contado ?>],
                ['Cuenta Corriente',       <?php echo $cant_ctacte ?>],
                ['Anulaciones	',       <?php echo $cant_anulaciones ?>],
           ]
        }]
    });
});
</script>

<script src="<?php echo base_url().'librerias/Highcharts-4.1.4/js/highcharts.js'?>"></script>
<script src="<?php echo base_url().'librerias/Highcharts-4.1.4/js/modules/exporting.js'?>"></script>