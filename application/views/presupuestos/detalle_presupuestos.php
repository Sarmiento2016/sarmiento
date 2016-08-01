<script>
	function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>


<div class="container"> 
<div class="col-md-12">
	<div class="panel panel-primary">
		<div class="panel-heading"><?php echo $texto['empresa_titulo'] ?></div>
		<div class="panel-body">
			
			<div id="printableArea">
			<?php 
			if($presupuestos)
			{
				echo "<table class='table table-hover'>";
				foreach ($presupuestos as $row) 
				{
					
					
					foreach ($impresiones as $impresion) 
	  				{
	  					$clientes	= $this->clientes_model->getRegistro($row->id_cliente);
		  				if($clientes)
						{
							foreach ($clientes as $row_cliente)
							{
								$nombre = $row_cliente->nombre;
								$apellido = $row_cliente->apellido;
							}
						}	
						$cabecera = $impresion->cabecera;
						$cabecera = str_replace("#presupuesto_nro#", $row->id_presupuesto, $cabecera);
						$cabecera = str_replace("#presupuesto_descuento#", $row->descuento, $cabecera);
						$cabecera = str_replace("#presupuesto_fecha#", date('d-m-Y', strtotime($row->fecha)), $cabecera);
						$cabecera = str_replace("#presupuesto_monto#", $row->monto, $cabecera);
						if(isset($nombre))
						{
							$cabecera = str_replace("#cliente_nombre#", $nombre, $cabecera);
						}
						else
						{
							$cabecera = str_replace("#cliente_nombre#", '', $cabecera);
						}
						
						if(isset($apellido))
						{
							$cabecera = str_replace("#cliente_apellido#", $apellido, $cabecera);
						}
						else
						{
							$cabecera = str_replace("#cliente_apellido#", '', $cabecera);
						}
						
						$pie = $impresion->pie;
						echo $cabecera;
						
						$id_presupuesto = $row->id_presupuesto;
					}
				
				echo "<hr>";
				
				$total=0;
				
				echo "<table class='table table-hover'>";
				echo "<tr>";
					echo "<th>".$texto['articulo']."</th>";
					echo "<th>Descripción</th>";
					echo "<th>".$texto['cantidad']."</th>";
					echo "<th>".$texto['monto']."</th>";
					echo "<th>".$texto['total']."</th>";
				echo "</tr>";
				
				if($detalle_presupuesto)
				{
				foreach ($detalle_presupuesto as $row_detalle) {
					echo "<tr>";	
						echo "<td><a title='ver Articulo' class='btn btn-default btn-xs' href='".base_url()."index.php/articulos/articulo_abm/read/".$row_detalle->id_articulo."'>".$row_detalle->cod_proveedor."</a></td>";
						echo "<td>".$row_detalle->descripcion."</td>";
						echo "<td>".$row_detalle->cantidad."</td>";
						$precio = $row_detalle->precio/$row_detalle->cantidad;
						echo "<td>".$precio."</td>";
						$sub_total = $row_detalle->cantidad * $precio;
						$total = $total + $sub_total;
						echo "<td>$ ".round($sub_total,2)."</td>";
					echo "</tr>";
				}
				}
				
				echo "<tr class='success'>";	
					echo "<td colspan='4'>".$texto['total']."</td>";
					echo "<th>$ ".round($total,2)."</th>";
				echo "</tr>";
				
				echo "</table>";
					
				echo "<hr>";
				echo $pie;
				}
				
				if($devoluciones)
				{
					$mensaje = $texto['si_devolucion']." <a class='btn btn-warning'>Ver devolución</a>";
					echo setMensaje($mensaje, 'warning');
				}
				
				
			}
			else
			{
				echo setMensaje($texto['no_registro'], 'success');
			}
			?>
			</div>
			<input type='button' class='btn btn-default' value='Volver a la lista' onclick='window.history.back()'>
			<?php 
  			if($row->estado != 3)
  			{
  			?>
				<button class="btn btn-default" type="button" onclick="printDiv('printableArea')"/>
	  				<i class="fa fa-print"></i> Imprimir
	  			</button>
	  			<?php 
	  			if($row->tipo == 2)
	  			{
	  			?>
	  			<a href="<?php echo base_url().'index.php/devoluciones/generar/'.$id_presupuesto?>" class="btn btn-default"/>
	  				<i class="fa fa-thumbs-down"></i> Devolución
	  			</a>
	  			<?php
	  			}
				?>
	  			<?php 
	  			if($row->tipo == 1)
	  			{
	  			?>
	  				<a href="<?php echo base_url().'index.php/presupuestos/anular/'.$id_presupuesto?>" class="btn btn-default"/>
	  					<i class="fa fa-trash-o"></i> Anular
	  				</a>
	  			<?php
	  			}
			}
			else
			{
				if($anulaciones)
				{
					foreach ($anulaciones as $row_a) 
					{
						$mensaje = "Nota de la anulación: ".$row_a->nota."<br>";
						$mensaje .= "Fecha de la anulación: ".date('d-m-Y', strtotime($row_a->fecha))."<br>";
					}
					
					echo setMensaje($mensaje, 'danger');
				}
			}
			?>
  			
		</div>
		
	</div>
	
</div>
</div>
</body>
</html>
