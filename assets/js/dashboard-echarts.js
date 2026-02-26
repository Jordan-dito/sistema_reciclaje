/**
 * Dashboard ECharts - Funciones de gráficos profesionales
 * Sistema de Gestión de Reciclaje
 */

// Gráfico 2: Compras por Material (Doughnut)
function cargarComprasPorMaterial() {
  var params = typeof buildQueryParams === 'function' ? buildQueryParams() : '';
  var esPeso = false; // Solo dinero, ya que no hay filtro individual
  
  $.ajax({
    url: 'compras/api.php?action=listar' + params,
    method: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.success && response.data) {
        var datosPorMaterial = {};
        response.data.forEach(function(compra) {
          if (compra.estado !== 'cancelada' && compra.detalles) {
            compra.detalles.forEach(function(detalle) {
              var material = detalle.material_nombre || 'Sin especificar';
              var valor = esPeso ? parseFloat(detalle.cantidad || 0) : parseFloat(detalle.subtotal || 0);
              datosPorMaterial[material] = (datosPorMaterial[material] || 0) + valor;
            });
          }
        });
        var data = Object.keys(datosPorMaterial).map(function(key, index) {
          return {
            name: key,
            value: datosPorMaterial[key],
            itemStyle: {
              color: coloresProfesionales[index % coloresProfesionales.length]
            }
          };
        });
        // Si solo hay un dato y es 'Sin especificar', ocultar el gráfico
        if (data.length === 0 || (data.length === 1 && data[0].name === 'Sin especificar')) {
          // Datos de ejemplo
          var dummyData = [
            { name: 'Plástico', value: 400, itemStyle: { color: '#667eea' } },
            { name: 'Vidrio', value: 300, itemStyle: { color: '#764ba2' } },
            { name: 'Metal', value: 357.5, itemStyle: { color: '#f093fb' } }
          ];
          var option = {
            tooltip: { trigger: 'item' },
            legend: { orient: 'vertical', left: 'left', textStyle: { color: '#fff' } },
            series: [
              {
                name: 'Compras por Material',
                type: 'pie',
                radius: ['45%', '70%'],
                center: ['50%', '45%'],
                data: dummyData,
                label: { color: '#fff' }
              }
            ]
          };
          $('#comprasMaterialChart').html('');
          if (!chartVentasMaterial) {
            chartVentasMaterial = echarts.init(document.getElementById('comprasMaterialChart'));
          }
          chartVentasMaterial.setOption(option);
          return;
        } else {
          $('#comprasMaterialChart').html('');
        }
        if (!chartVentasMaterial) {
          chartVentasMaterial = echarts.init(document.getElementById('comprasMaterialChart'));
        }
        var option = {
          tooltip: {
            trigger: 'item',
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            borderWidth: 0,
            textStyle: {
              color: '#fff',
              fontSize: 13
            },
            formatter: function(params) {
              var valorFormateado = esPeso
                ? params.value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' kg'
                : '$' + params.value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              return params.marker + ' ' + params.name + '<br/>' +
                     valorFormateado +
                     ' (' + params.percent + '%)';
            }
          },
          legend: {
            orient: 'horizontal',
            bottom: 10,
            textStyle: {
              fontSize: 12
            },
            itemWidth: 12,
            itemHeight: 12
          },
          series: [
            {
              name: esPeso ? 'Ventas (Kg)' : 'Ventas ($)',
              type: 'pie',
              radius: ['45%', '70%'],
              center: ['50%', '45%'],
              avoidLabelOverlap: true,
              itemStyle: {
                borderRadius: 8,
                borderColor: '#fff',
                borderWidth: 3
              },
              label: {
                show: false
              },
              emphasis: {
                label: {
                  show: true,
                  fontSize: 14,
                  fontWeight: 'bold'
                },
                itemStyle: {
                  shadowBlur: 15,
                  shadowOffsetX: 0,
                  shadowColor: 'rgba(0, 0, 0, 0.3)'
                }
              },
              labelLine: {
                show: false
              },
              data: data
            }
          ]
        };
        chartVentasMaterial.setOption(option);
      }
    }
  });
}

// Gráfico 4: Análisis por Sucursal (Bar)
function cargarAnalisisPorSucursal() {
  var params = typeof buildQueryParams === 'function' ? buildQueryParams(false) : '';
  
  Promise.all([
    $.ajax({ url: 'compras/api.php?action=listar' + params, method: 'GET', dataType: 'json' }),
    $.ajax({ url: 'ventas/api.php?action=listar' + params, method: 'GET', dataType: 'json' })
  ]).then(function([comprasResp, ventasResp]) {
    var sucursales = [];
    var comprasPorSucursal = {};
    var ventasPorSucursal = {};
    
    if (comprasResp.success && comprasResp.data) {
      comprasResp.data.forEach(function(compra) {
        if (compra.estado !== 'cancelada') {
          var sucursal = compra.sucursal_nombre || 'Sin sucursal';
          comprasPorSucursal[sucursal] = (comprasPorSucursal[sucursal] || 0) + parseFloat(compra.total || 0);
          if (!sucursales.includes(sucursal)) sucursales.push(sucursal);
        }
      });
    }
    
    if (ventasResp.success && ventasResp.data) {
      ventasResp.data.forEach(function(venta) {
        if (venta.estado !== 'cancelada') {
          var sucursal = venta.sucursal_nombre || 'Sin sucursal';
          ventasPorSucursal[sucursal] = (ventasPorSucursal[sucursal] || 0) + parseFloat(venta.total || 0);
          if (!sucursales.includes(sucursal)) sucursales.push(sucursal);
        }
      });
    }
    
    var datosCompras = sucursales.map(s => comprasPorSucursal[s] || 0);
    var datosVentas = sucursales.map(s => ventasPorSucursal[s] || 0);
    var datosGanancia = sucursales.map(s => (ventasPorSucursal[s] || 0) - (comprasPorSucursal[s] || 0));
    
    if (!chartAnalisisSucursal) {
      chartAnalisisSucursal = echarts.init(document.getElementById('analisisSucursalChart'));
    }
    
    var option = {
      tooltip: {
        trigger: 'axis',
        backgroundColor: 'rgba(0, 0, 0, 0.85)',
        borderWidth: 0,
        textStyle: {
          color: '#fff',
          fontSize: 13
        },
        axisPointer: {
          type: 'shadow'
        },
        formatter: function(params) {
          var result = params[0].name + '<br/>';
          params.forEach(function(item) {
            result += item.marker + ' ' + item.seriesName + ': $' + 
                     item.value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '<br/>';
          });
          return result;
        }
      },
      legend: {
        data: ['Compras', 'Ventas', 'Ganancia'],
        top: 10,
        textStyle: {
          fontSize: 13
        }
      },
      grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        top: '15%',
        containLabel: true
      },
      xAxis: {
        type: 'category',
        data: sucursales,
        axisLine: {
          lineStyle: {
            color: '#e0e0e0'
          }
        },
        axisLabel: {
          color: '#666',
          fontSize: 11,
          interval: 0,
          rotate: sucursales.length > 5 ? 45 : 0
        }
      },
      yAxis: {
        type: 'value',
        axisLine: {
          show: false
        },
        axisTick: {
          show: false
        },
        axisLabel: {
          color: '#666',
          fontSize: 12,
          formatter: function(value) {
            return '$' + value.toLocaleString('es-ES');
          }
        },
        splitLine: {
          lineStyle: {
            color: 'rgba(0, 0, 0, 0.05)'
          }
        }
      },
      series: [
        {
          name: 'Compras',
          type: 'bar',
          data: datosCompras,
          itemStyle: {
            color: {
              type: 'linear',
              x: 0,
              y: 0,
              x2: 0,
              y2: 1,
              colorStops: [
                { offset: 0, color: '#f3545d' },
                { offset: 1, color: '#ff8a80' }
              ]
            },
            borderRadius: [8, 8, 0, 0]
          },
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowColor: 'rgba(243, 84, 93, 0.5)'
            }
          }
        },
        {
          name: 'Ventas',
          type: 'bar',
          data: datosVentas,
          itemStyle: {
            color: {
              type: 'linear',
              x: 0,
              y: 0,
              x2: 0,
              y2: 1,
              colorStops: [
                { offset: 0, color: '#1dce6c' },
                { offset: 1, color: '#69f0ae' }
              ]
            },
            borderRadius: [8, 8, 0, 0]
          },
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowColor: 'rgba(29, 206, 108, 0.5)'
            }
          }
        },
        {
          name: 'Ganancia',
          type: 'bar',
          data: datosGanancia,
          itemStyle: {
            color: {
              type: 'linear',
              x: 0,
              y: 0,
              x2: 0,
              y2: 1,
              colorStops: [
                { offset: 0, color: '#fdaf4b' },
                { offset: 1, color: '#ffd54f' }
              ]
            },
            borderRadius: [8, 8, 0, 0]
          },
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowColor: 'rgba(253, 175, 75, 0.5)'
            }
          }
        }
      ]
    };
    
    chartAnalisisSucursal.setOption(option);
  });
}

// Gráfico 5: Top 5 Productos (Horizontal Bar)
function cargarTopProductos() {
  var params = typeof buildQueryParams === 'function' ? buildQueryParams(false) : '';
  
  $.ajax({
    url: 'ventas/api.php?action=listar' + params,
    method: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.success && response.data) {
        var productosCantidad = {};
        
        response.data.forEach(function(venta) {
          if (venta.estado !== 'cancelada' && venta.detalles) {
            venta.detalles.forEach(function(detalle) {
              var producto = detalle.producto_nombre || 'Sin nombre';
              productosCantidad[producto] = (productosCantidad[producto] || 0) + parseFloat(detalle.cantidad || 0);
            });
          }
        });
        
        var productosArray = Object.keys(productosCantidad).map(function(key) {
          return { nombre: key, cantidad: productosCantidad[key] };
        });
        
        productosArray.sort((a, b) => b.cantidad - a.cantidad);
        var top5 = productosArray.slice(0, 5);
        
        var labels = top5.map(p => p.nombre);
        var valores = top5.map(p => p.cantidad);
        
        if (!chartTopProductos) {
          chartTopProductos = echarts.init(document.getElementById('topProductosChart'));
        }
        
        var option = {
          tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            borderWidth: 0,
            textStyle: {
              color: '#fff',
              fontSize: 13
            },
            axisPointer: {
              type: 'shadow'
            },
            formatter: function(params) {
              return params[0].marker + ' ' + params[0].name + '<br/>' +
                     'Cantidad: ' + params[0].value.toLocaleString('es-ES');
            }
          },
          grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '5%',
            containLabel: true
          },
          xAxis: {
            type: 'value',
            axisLine: {
              show: false
            },
            axisTick: {
              show: false
            },
            axisLabel: {
              color: '#666',
              fontSize: 11
            },
            splitLine: {
              lineStyle: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            }
          },
          yAxis: {
            type: 'category',
            data: labels,
            axisLine: {
              lineStyle: {
                color: '#e0e0e0'
              }
            },
            axisLabel: {
              color: '#666',
              fontSize: 11
            },
            axisTick: {
              show: false
            }
          },
          series: [
            {
              name: 'Cantidad',
              type: 'bar',
              data: valores,
              itemStyle: {
                color: {
                  type: 'linear',
                  x: 0,
                  y: 0,
                  x2: 1,
                  y2: 0,
                  colorStops: [
                    { offset: 0, color: '#667eea' },
                    { offset: 1, color: '#764ba2' }
                  ]
                },
                borderRadius: [0, 8, 8, 0]
              },
              emphasis: {
                itemStyle: {
                  shadowBlur: 10,
                  shadowColor: 'rgba(102, 126, 234, 0.5)'
                }
              },
              label: {
                show: true,
                position: 'right',
                color: '#666',
                fontSize: 12,
                fontWeight: 'bold'
              }
            }
          ]
        };
        
        chartTopProductos.setOption(option);
      }
    }
  });
}

// Gráfico 6: Inventario por Categoría (Pie)
function cargarInventarioPorCategoria() {
  // Excluye material global, pero admite sub-filtros locales (fecha/sucursal)
  var params = typeof buildQueryParams === 'function' ? buildQueryParams() : '';
  
  $.ajax({
    url: 'inventarios/api.php?action=listar' + params,
    method: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.success && response.data) {
        var datosPorCategoria = {};
        response.data.forEach(function(inventario) {
          var categoria = inventario.categoria_nombre || 'Sin categoría';
          datosPorCategoria[categoria] = (datosPorCategoria[categoria] || 0) + parseFloat(inventario.cantidad || 0);
        });
        var data = Object.keys(datosPorCategoria).map(function(key, index) {
          return {
            name: key,
            value: datosPorCategoria[key],
            itemStyle: {
              color: coloresProfesionales[index % coloresProfesionales.length]
            }
          };
        });
        if (data.length === 0) {
          // Datos de ejemplo
          var dummyData = [
            { name: 'Reciclable', value: 600, itemStyle: { color: '#667eea' } },
            { name: 'No Reciclable', value: 457.5, itemStyle: { color: '#764ba2' } }
          ];
          var option = {
            tooltip: { trigger: 'item' },
            legend: { orient: 'vertical', left: 'left', textStyle: { color: '#fff' } },
            series: [
              {
                name: 'Inventario por Categoría',
                type: 'pie',
                radius: ['45%', '70%'],
                center: ['50%', '45%'],
                data: dummyData,
                label: { color: '#fff' }
              }
            ]
          };
          $('#inventarioChart').html('');
          if (!chartInventario) {
            chartInventario = echarts.init(document.getElementById('inventarioChart'));
          }
          chartInventario.setOption(option);
          return;
        } else {
          $('#inventarioChart').html('');
        }
        if (!chartInventario) {
          chartInventario = echarts.init(document.getElementById('inventarioChart'));
        }
        var option = {
          tooltip: {
            trigger: 'item',
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            borderWidth: 0,
            textStyle: {
              color: '#fff',
              fontSize: 13
            },
            formatter: function(params) {
              return params.marker + ' ' + params.name + '<br/>' +
                     'Cantidad: ' + params.value.toLocaleString('es-ES') +
                     ' (' + params.percent + '%)';
            }
          },
          legend: {
            orient: 'horizontal',
            bottom: 10,
            textStyle: {
              fontSize: 12
            },
            itemWidth: 12,
            itemHeight: 12
          },
          series: [
            {
              name: 'Inventario',
              type: 'pie',
              radius: '65%',
              center: ['50%', '45%'],
              itemStyle: {
                borderRadius: 8,
                borderColor: '#fff',
                borderWidth: 2
              },
              label: {
                show: false
              },
              emphasis: {
                label: {
                  show: true,
                  fontSize: 14,
                  fontWeight: 'bold'
                },
                itemStyle: {
                  shadowBlur: 15,
                  shadowOffsetX: 0,
                  shadowColor: 'rgba(0, 0, 0, 0.3)'
                }
              },
              data: data
            }
          ]
        };
        chartInventario.setOption(option);
      }
    }
  });
}

// Gráfico 7: Estado de Transacciones (Doughnut)
function cargarEstadoTransacciones() {
  var params = typeof buildQueryParams === 'function' ? buildQueryParams(false) : '';
  
  Promise.all([
    $.ajax({ url: 'compras/api.php?action=listar&estado=todos' + params, method: 'GET', dataType: 'json' }),
    $.ajax({ url: 'ventas/api.php?action=listar&estado=todos' + params, method: 'GET', dataType: 'json' })
  ]).then(function([comprasResp, ventasResp]) {
    var completadas = 0, pendientes = 0, canceladas = 0;
    
    if (comprasResp.success && comprasResp.data) {
      comprasResp.data.forEach(function(compra) {
        if (compra.estado === 'completada') completadas++;
        else if (compra.estado === 'pendiente') pendientes++;
        else if (compra.estado === 'cancelada') canceladas++;
      });
    }
    
    if (ventasResp.success && ventasResp.data) {
      ventasResp.data.forEach(function(venta) {
        if (venta.estado === 'completada') completadas++;
        else if (venta.estado === 'pendiente') pendientes++;
        else if (venta.estado === 'cancelada') canceladas++;
      });
    }
    
    if (!chartEstadoTransacciones) {
      chartEstadoTransacciones = echarts.init(document.getElementById('estadoTransaccionesChart'));
    }
    
    var option = {
      tooltip: {
        trigger: 'item',
        backgroundColor: 'rgba(0, 0, 0, 0.85)',
        borderWidth: 0,
        textStyle: {
          color: '#fff',
          fontSize: 13
        },
        formatter: function(params) {
          return params.marker + ' ' + params.name + '<br/>' +
                 'Cantidad: ' + params.value + ' (' + params.percent + '%)';
        }
      },
      legend: {
        orient: 'horizontal',
        bottom: 10,
        textStyle: {
          fontSize: 12
        },
        itemWidth: 12,
        itemHeight: 12
      },
      series: [
        {
          name: 'Transacciones',
          type: 'pie',
          radius: ['40%', '65%'],
          center: ['50%', '45%'],
          avoidLabelOverlap: true,
          itemStyle: {
            borderRadius: 8,
            borderColor: '#fff',
            borderWidth: 3
          },
          label: {
            show: false
          },
          emphasis: {
            label: {
              show: true,
              fontSize: 16,
              fontWeight: 'bold'
            },
            itemStyle: {
              shadowBlur: 15,
              shadowOffsetX: 0,
              shadowColor: 'rgba(0, 0, 0, 0.3)'
            }
          },
          data: [
            { 
              value: completadas, 
              name: 'Completadas',
              itemStyle: { color: '#1dce6c' }
            },
            { 
              value: pendientes, 
              name: 'Pendientes',
              itemStyle: { color: '#fdaf4b' }
            },
            { 
              value: canceladas, 
              name: 'Canceladas',
              itemStyle: { color: '#f3545d' }
            }
          ]
        }
      ]
    };
    
    chartEstadoTransacciones.setOption(option);
  });
}

// Inicialización de la sección de distribución
$(document).ready(function() {
  // Cargar sucursales en el filtro
  $.ajax({
    url: 'inventarios/api.php?action=sucursales',
    method: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.success && response.data) {
        var $select = $('#filtroDistribucionSucursal');
        response.data.forEach(function(suc) {
          $select.append('<option value="' + suc.id + '">' + suc.nombre + '</option>');
        });
      }
    }
  });

  // Evento para cargar gráfico al cambiar filtros
  $('#filtroDistribucionTipo, #filtroDistribucionFecha, #filtroDistribucionSucursal').on('change', function() {
    cargarDistribucionChart();
  });

  // Cargar gráfico inicial
  cargarDistribucionChart();
});

// Nueva función: Gráfico de Distribución con Filtro Específico
function cargarDistribucionChart() {
  var tipo = $('#filtroDistribucionTipo').val();
  var fecha = $('#filtroDistribucionFecha').val();
  var sucursal = $('#filtroDistribucionSucursal').val();
  var url = '';
  var params = '';
  if (tipo === 'compras') {
    url = 'compras/api.php?action=listar';
    params = '';
    if (fecha) params += '&fecha=' + fecha;
    if (sucursal) params += '&sucursal_id=' + sucursal;
  } else {
    url = 'inventarios/api.php?action=listar';
    params = '';
    if (fecha) params += '&fecha_inicio=' + fecha;
    if (sucursal) params += '&sucursal_id=' + sucursal;
  }
  $.ajax({
    url: url + params,
    method: 'GET',
    dataType: 'json',
    success: function(response) {
      var chart = echarts.init(document.getElementById('distribucionChart'));
      if (response.success && response.data && response.data.length > 0) {
        var datos = {};
        if (tipo === 'compras') {
          response.data.forEach(function(compra) {
            if (compra.detalles) {
              compra.detalles.forEach(function(detalle) {
                var material = detalle.material_nombre || 'Sin especificar';
                var valor = parseFloat(detalle.subtotal || 0);
                datos[material] = (datos[material] || 0) + valor;
              });
            }
          });
        } else {
          response.data.forEach(function(inv) {
            var categoria = inv.categoria_nombre || 'Sin especificar';
            var valor = parseFloat(inv.cantidad || 0);
            datos[categoria] = (datos[categoria] || 0) + valor;
          });
        }
        var data = Object.keys(datos).map(function(key, idx) {
          return {
            name: key,
            value: datos[key]
          };
        });
        var option = {
          tooltip: { trigger: 'item' },
          legend: { orient: 'vertical', left: 'left', textStyle: { color: '#fff' } },
          series: [
            {
              name: tipo === 'compras' ? 'Compras por Material' : 'Inventario por Categoría',
              type: 'pie',
              radius: ['45%', '70%'],
              center: ['50%', '45%'],
              data: data,
              label: { color: '#fff' }
            }
          ]
        };
        chart.setOption(option);
      } else {
        chart.clear();
        $('#distribucionChart').html('<div class="text-center text-muted mt-4">No hay datos para los filtros seleccionados.</div>');
      }
    }
  });
}
