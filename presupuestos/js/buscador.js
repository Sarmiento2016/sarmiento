var codigo_r		= [];
var cantidad_r		= [];
var precio_r		= [];

var item_elegido;
var items_reglon	= [];
var px_unitario;
var porc_iva_art;
var nuevo			= true;
$("#cantidad").attr('disabled','disabled');
$("#vendedor").focus();
$('#carga_presupuesto').hide();
$('#cancela_presupuesto').hide();


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Limpia clientes

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/


function limpia_cli()
{
	$('#apellido_cliente').val('');
	$('#nombre_cliente').val('');
	$('#domicilio_cliente').val('');
	$('#cuit_cliente').val('');
	$('#id_cliente').val(0);
	$('#tipo_presupuesto_contado').click();
	$('#carga_cliente').val('');
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Realiza el control del presupuesto

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/


function carga_presupuesto()
{
	if($('#total_presupuesto').val()>0) {
		bandera_tipo_pago = $("input[name='tipo']:checked").val();
		cli_id = $("#id_cliente").val();
		
		if (bandera_tipo_pago == 2) {
			if( cli_id != 0) {
				if($('#descuento').val()>0) {
					if(confirm('¿Seguro que desea CTACTE + DESCUENTO?')) {
						guarda_detalle(),fin_presupuesto();
					} 					
				} else {
					if(confirm('¿Seguro que desea asignar a cuenta corriente?')){
						guarda_detalle(),fin_presupuesto();
					} else {
						$('#tipo_presupuesto').focus();
					}
				}
			} else {
				alert("Seleccione un cliente o cambien condicion de pago");
			}
		} else{
			if(cli_id != 0) {
				guarda_detalle(),fin_presupuesto();
			} else {
				guarda_detalle(),fin_presupuesto();
			}
		}
	} else {
		alert("Presupuesto vacio");
	}				
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Envia el ajax

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/


function fin_presupuesto()
{
	atotal		= $("#total_presupuesto").val();
	acliente	= $("#id_cliente").val();
	atipo		= $("input[name='tipo']:checked").val(); 
	dto			= parseFloat($('#descuento').val());
	vendedor	= $('#vendedor').val();
	comentario	= $("#comentario").val();
	if ($('#com_publico').prop('checked')) {
    	com_publico	= 1;
	}else
	{
		com_publico	= 0;
	}
	
	
	if(atipo == 1) { // contado
		aestado = 2;// cancelado
	} else {
		aestado = 1;// falta de pago
	}	

	$.ajax({
		url : 'carga_presupuesto.php',
		type: 'POST',
		data : {
			total:atotal,
			cliente:acliente,
			tipo:atipo,
			estado:aestado,
			codigos_art:codigo_r,
			cantidades:cantidad_r,
			precios:precio_r,
			desc:dto, 
			vendedor:vendedor,
			comentario:comentario,
			com_publico:com_publico
		}
    }).done( function( data ) {
		// console.log(data);
	});
	
	location.reload();
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Carga los datos del cliente

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/


$(function() 
{	
	$("#carga_cliente").autocomplete({
	    source: "search_cliente.php",
	    minLength: 2,//search after two characters
	    select: function(event,ui){
	        console.log(ui);
			nombre_cli	= ui.item.nombre;
	        apellido_cli= ui.item.apellido;
			id_cliente	= ui.item.id_cliente;
	        num_cuil	= ui.item.num_cuil;
	    	dom_cli		= ui.item.direccion;
			console.log(apellido_cli);
			
			$('#apellido_cliente').val(apellido_cli);
			$('#nombre_cliente').val(nombre_cli);
			$('#domicilio_cliente').val(dom_cli);
			$('#cuit_cliente').val(num_cuil);
			$('#id_cliente').val(id_cliente);
			$('#tipo_presupuesto_ctacte').click();
		},
	    
		close: function( event, ui ) {
		}
		
	});		
});


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Carga la lista de articulos

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/


$(function() {
	$("#quickfind").autocomplete({
	    source: "search_articulo.php",
	    minLength: 2,//search after two characters
	    select: function(event,ui){
	        porc_iva_art	= ui.item.iva;
	        item_elegido	= ui.item;
	        este			= ui.item.id;
			px_unitario		= ui.item.precio;
			
			$('#px_unitario_rapido').val(px_unitario * ((porc_iva_art/100) + 1 ));	
			pos				= items_reglon.indexOf(este);
	    	
			$("#cantidad").removeAttr('disabled');
			$('#cantidad').focus();
			$('#cantidad').select();
			 
	        //console.log(px_unitario);
	        
			if (pos != -1) {    
	        	nuevo		= false; 
				cant_cargada= $('#cant_'+este).val();
	            $('#cantidad').val(cant_cargada);
				$('#cantidad').select();
			}
		},
	    
		close: function( event, ui ) {
		}
	});		
});


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Para cantidad

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/

        
$("#cantidad").keypress(function( event ) {
	if ( event.which == 13 ) {
		$("#carga_articulo").click();
		$("#cantidad").attr('disabled','disabled');
	}
});	


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Carga detalle

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/	


function carga(elem)
{
	cantidad = $('#cantidad').val();
	
	if(cantidad != 0 && cantidad > 0) {
		if($("#quickfind").val().length >= 1) {
			este = elem.id;
	 
			if(nuevo){
				items_reglon.push(este);
			}
			agrega_a_reglon(este,elem.value,cantidad, nuevo,px_unitario,porc_iva_art);
			reset_item();
		} else {
			reset_item(); 
		}
	}else{
		reset_item(); 
	}
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Agrega renglon en el presupuesto

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/	


function agrega_a_reglon(este,texto,cantidad, bandera,px_unitario_sin_iva,porc_iva)
{
	px_unitario	= px_unitario_sin_iva * ((porc_iva / 100) + 1);
	px_reglon	= cantidad * px_unitario;
    iva			= (px_unitario_sin_iva * (porc_iva / 100)) * cantidad;
		
	if(bandera){
		largo	= $('#reglon_factura').height();
		
		largo	= largo + 30;
		//console.log("largo despúes:"+largo);
		
		$('#reglon_factura').height(largo);
		$('#reglon_factura').append('<div id="cont_borra'+este+'" class="cont_reglon_item_presup row" style="padding-left: 15px"></div>');
		$('#cont_borra'+este).append('<span class="item_reglon col-md-5" id='+este+' >'+texto+'</span>');
		$('#cont_borra'+este).append('<input  disabled class="cant_item_reglon col-md-1" id=cant_'+este+' value='+cantidad+'>');
		$('#cont_borra'+este).append('<input disabled  class="px_item_reglon col-md-1" id=px_'+este+' value='+px_unitario+'>');
		$('#cont_borra'+este).append('<input disabled  class="porc_iva_item_reglon col-md-1" id=porc_iva_'+este+' value=%'+porc_iva+'>');
		$('#cont_borra'+este).append('<input disabled  class="px_item_reglon_iva col-md-1" id=px_iva'+este+' value='+iva+'>');
		$('#cont_borra'+este).append('<input disabled  class="px_reglon col-md-2" id=px_x_cant'+este+' value='+px_reglon+'>');
		$('#cont_borra'+este).append('<div class="col-md-1" id=cont_botones'+este+'></div>');
		$('#cont_botones'+este).append('<button title="Borrar linea" class="ico_borra btn btn-danger btn-xs pull-left" onclick="borra_reglon('+este+')" id="ico_borra'+este+'"></button>');
		$('#ico_borra'+este).append('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>');
		
		reset_item();
    } else {
		$('#px_iva'+este).val(iva);		
		$('#cant_'+este).val(cantidad);
		$('#px_x_cant'+este).val(px_reglon);    	
		
		nuevo = true; 
		reset_item();   
	} 	
	
	calcula_total(porc_iva);
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Elimina renglon en el presupuesto

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/	


function borra_reglon(a)
{	
	pos = items_reglon.indexOf(a);
	console.log("posicion a borrar: "+pos+1+" de: "+items_reglon.length);
	items_reglon.splice( pos, 1 );
	$.each(items_reglon, function(index, val) {
    	console.log(val);
	});
	
	
	$('#cont_borra'+a).empty();
	$('#cont_borra'+a).remove();
	var nuevo_largo=$('#reglon_factura').height();
	nuevo_largo=nuevo_largo-30;
	$('#reglon_factura').height(nuevo_largo);
	calcula_total(),descuento();
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Calcula el total del presupuesto

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/	


function calcula_total(iva)
{
	var total	= 0;
	var temp	= 0;
	
	$(".px_reglon").each(function (index) {
		temp = $(this).val();
		total=parseFloat(total)+parseFloat(temp);
	});
	
	$('#total_presupuesto').val(total.toFixed(2));
	total_iva_presupuesto = 0;
	
	$('.px_item_reglon_iva').each(function (index) {		
		  temp1 = $(this).val();
		  total_iva_presupuesto = total_iva_presupuesto + parseFloat(temp1);
	});
	
	total_iva_presupuesto=total_iva_presupuesto.toFixed(2);
	$('#total_iva').val(total_iva_presupuesto);
	
	if(total > 0) {
		$('#descuento').prop( "disabled", false );
		$('#cont_boton').show();
	} else {
		$('#cont_boton').hide();
	}
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Carga detalle del presupuesto

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/	


function guarda_detalle()
{
	dto_final = parseFloat($('#descuento').val());
	var temp = 0;
	
	if(dto_final > 0) {
		$('.px_reglon').each(function (index) {
			precio_r.push($(this).val()*(1-(dto_final/100)));
		});
	} else {
		$('.px_reglon').each(function (index) {
			precio_r.push($(this).val());
		});
	}
	
	
	$('.cant_item_reglon').each(function (index) {
		cantidad_r.push($(this).val());
		temp_id		= this.id;
		cod_prod	= parseInt(temp_id.slice(5));
		codigo_r.push(cod_prod);
	});
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Limpia valores

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/	


function reset_item()
{
    $('#px_unitario_rapido').val('');
    $("#quickfind").val('');
    $("#quickfind").focus();
    $('#cantidad').val(1);    
}


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Carga la fecha actual

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/	


function inicializa()
{
	var f = new Date();
	var fecha = f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear();
	
	$("#fecha_presupuesto").val(fecha);
}  


/*---------------------------------------------------------------------------------
----------------------------------------------------------------------------------- 

		Aplica descuento si lo hay

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------*/


function descuento()
{
	calcula_total();
	
	dto = parseFloat($('#descuento').val());
	
	if($('#total_presupuesto').val()>0){	
		$('#total_presupuesto').val($('#total_presupuesto').val() * ( 1 - (dto / 100)));
		$('#total_iva').val($('#total_iva').val() * ( 1 - (dto / 100)));
	} else {
		$('#descuento').prop( "disabled", true);
		$('#descuento').val(0);
	}
}