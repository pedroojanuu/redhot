@extends('layouts.adminHeaderFooter')

@section('title', 'Estatísticas |')

@section('content')
    <div class="adminContent">
        <div class="adminPage">
            <div class="adminSideBar">
                <div class="adminSearchBarOnSideBar">
                    <form action="{{ route('searchProductById') }}" method="POST">
                        @csrf
                        <input type="text" id="id" name="id" placeholder="Produto..">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>
                <div class="adminSideBarOptions">
                    <a href="{{ url('/admin') }}">
                        <div class="adminSideBarOption" id="optionSelected">
                            <div id="rectangle"></div>
                            <p>Estatísticas</p>
                        </div>
                    </a>

                    <a href="{{ url('/adminOrders') }}">
                        <div class="adminSideBarOption" id="optionNotSelected">
                            <p>Encomendas</p>
                        </div>
                    </a>

                    <a href="{{ url('/adminProductsManage') }}">
                        <div class="adminSideBarOption" id="optionNotSelected">
                            <p>Produtos</p>
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>

                    <a href="{{ url('/adminUsers') }}">
                        <div class="adminSideBarOption" id="optionNotSelected">
                            <p>Utilizadores</p>
                        </div>
                    </a>

                    <a href="{{ url('/adminPromoCode')}}">
                        <div class="adminSideBarOption" id="optionNotSelected">
                            <p>Códigos Promocionais</p>
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>

                    <a href="{{ url('/adminFAQ') }}">
                        <div class="adminSideBarOption" id="optionNotSelected">
                            <p>FAQ's</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="adminOptionContent">
                <div class="adminOptionContentTitle">
                    <h1>Estatísticas</h1>
                </div>

                <div class="adminOptionRealContent">

                    <div class="generalStatistics">
                        <div class="smallTitle">
                            <h3>Estatísticas Gerais</h3>
                        </div>
                        <div class="statisticsContent">
                            <div class="statsCont">
                                <div class="statText">
                                    <h2>Visitas</h2>
                                    <p>Ultimos 30 dias: </p>
                                </div>
                                <div class="statNumber">
                                    <h2>{{ $visitsLast30Days }}</h2>
                                </div>
                            </div>

                            <div class="statsCont">
                                <div class="statText">
                                    <h2>Carrinhos Ativos</h2>
                                    <p>Ultimos 30 dias: </p>
                                </div>
                                <div class="statNumber">
                                    <h2>{{ $activeShoppingCarts }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="averageSalesTable">
                            <div class="tableTitleAvg">
                                <h3>Vendas Médias</h3>
                            </div>
                            <div class="tableValuesAvg">
                                <div class="tableValueAvg">
                                    <p>Este Mês:</p>
                                    <div class="tableValueAvgNumber">
                                        <h2>{{ $averageSaleValueLastMonth }}€</h2>
                                        @if ($averageSaleValueLastMonth > $averageSaleValue)
                                            <div class="green">
                                                <i class="fas fa-arrow-up"></i>
                                            </div>
                                        @elseif($averageSaleValueLastMonth < $averageSaleValue)
                                            <div class="red">
                                                <i class="fas fa-arrow-down"></i>
                                            </div>
                                        @else
                                            <i class="fas fa-minus"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="separator"></div>
                                <div class="tableValueAvg">
                                    <p>De Sempre:</p>
                                    <h2>{{ $averageSaleValue }}€</h2>
                                </div>
                            </div>
                        </div>

                        <div class="tableTop5SoldProducts">
                            <div class="tableTitleTop5">
                                <h3>Top 5 Produtos Mais Vendidos</h3>
                            </div>
                            <div class="top5Products">
                                @for ($i = 0; $i < 5; $i++)
                                    @php
                                        $product = $top5[$i];
                                        $totalprice = $top5prices[$i];
                                        $totalQuantity = $top5quantities[$i];
                                    @endphp

                                    <div class="top5ProductInfo">
                                        <a href="{{ route('productsdetails', ['id' => $product->id]) }}">{{ $product->nome }}</a>
                                        <p>{{ $totalQuantity }}</p>
                                    </div>
                                @endfor
                            </div>
                        </div>



                    </div>

                    <div class="grafs">
                        <div class="salesGraf">
                            <div class="smallTitle">
                                <h3>Vendas</h3>
                            </div>
                            <div class="month-selection">
                                <label for="month">De:</label>
                                <select id="salesMonth" name="month">
                                    <option value="0">Todos os Meses</option>
                                    <option value="1">Janeiro</option>
                                    <option value="2">Fevereiro</option>
                                    <option value="3">Março</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Maio</option>
                                    <option value="6">Junho</option>
                                    <option value="7">Julho</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Setembro</option>
                                    <option value="10">Outubro</option>
                                    <option value="11">Novembro</option>
                                    <option value="12">Dezembro</option>
                                </select>
                            </div>
                            <div class="graf">
                                <canvas id="salesChart"></canvas>
                            </div>

                        </div>

                        <div class="moneyGraf">
                            <div class="smallTitle">
                                <h3>Faturação</h3>
                            </div>
                            <div class="month-selection">
                                <label for="month">De:</label>
                                <select id="moneyMonth" name="month">
                                    <option value="0">Todos os Meses</option>
                                    <option value="1">Janeiro</option>
                                    <option value="2">Fevereiro</option>
                                    <option value="3">Março</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Maio</option>
                                    <option value="6">Junho</option>
                                    <option value="7">Julho</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Setembro</option>
                                    <option value="10">Outubro</option>
                                    <option value="11">Novembro</option>
                                    <option value="12">Dezembro</option>
                                </select>
                            </div>
                            <div class="graf">
                                <canvas id="moneyChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMonth = 0;

        let labels = <?php echo json_encode($yearLabels); ?>;
        let months = <?php echo json_encode($yearLabels); ?>;
        let salesChart;
        let moneyChart;

        function updateSalesChart() {
            salesChart.data.labels = labels;
            salesChart.data.datasets[0].label = (selectedMonth === 0 ? 'Vendas de 2023' : 'Vendas de ' + months[
                selectedMonth -
                1] + ' - 2023');


            switch (selectedMonth) {
                case 0:
                    salesChart.data.datasets[0].data = [
                        {{ $sales[0]->count() }}, {{ $sales[1]->count() }}, {{ $sales[2]->count() }},
                        {{ $sales[3]->count() }},
                        {{ $sales[4]->count() }}, {{ $sales[5]->count() }}, {{ $sales[6]->count() }},
                        {{ $sales[7]->count() }},
                        {{ $sales[8]->count() }}, {{ $sales[9]->count() }}, {{ $sales[10]->count() }},
                        {{ $sales[11]->count() }}
                    ];
                    break;
                case 1:
                    salesChart.data.datasets[0].data = [
                        @foreach ($janeiro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 2:
                    salesChart.data.datasets[0].data = [
                        @foreach ($fevereiro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 3:
                    salesChart.data.datasets[0].data = [
                        @foreach ($marco as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 4:
                    salesChart.data.datasets[0].data = [
                        @foreach ($abril as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 5:
                    salesChart.data.datasets[0].data = [
                        @foreach ($maio as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 6:
                    salesChart.data.datasets[0].data = [
                        @foreach ($junho as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 7:
                    salesChart.data.datasets[0].data = [
                        @foreach ($julho as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 8:
                    salesChart.data.datasets[0].data = [
                        @foreach ($agosto as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 9:
                    salesChart.data.datasets[0].data = [
                        @foreach ($setembro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 10:
                    salesChart.data.datasets[0].data = [
                        @foreach ($outubro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 11:
                    salesChart.data.datasets[0].data = [
                        @foreach ($novembro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 12:
                    salesChart.data.datasets[0].data = [
                        @foreach ($dezembro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
            }

            salesChart.update();
        }

        function handleSalesMonthSelection() {
            const dropdown = document.getElementById('salesMonth');
            selectedMonth = parseInt(dropdown.value);

            if (selectedMonth === 0) {
                labels = <?php echo json_encode($yearLabels); ?>;
            } else {
                labels = <?php echo json_encode($monthLabels); ?>;
            }

            updateSalesChart();
        }

        function handleMoneyMonthSelection() {
            const dropdown = document.getElementById('moneyMonth');
            selectedMonth = parseInt(dropdown.value);

            if (selectedMonth === 0) {
                labels = <?php echo json_encode($yearLabels); ?>;
            } else {
                labels = <?php echo json_encode($monthLabels); ?>;
            }

            updateMoneyChart();
        }

        const ctx = document.getElementById('salesChart');
        salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Vendas de 2023',
                    data: [
                        {{ $sales[0]->count() }}, {{ $sales[1]->count() }}, {{ $sales[2]->count() }},
                        {{ $sales[3]->count() }}, {{ $sales[4]->count() }}, {{ $sales[5]->count() }},
                        {{ $sales[6]->count() }}, {{ $sales[7]->count() }}, {{ $sales[8]->count() }},
                        {{ $sales[9]->count() }}, {{ $sales[10]->count() }},
                        {{ $sales[11]->count() }}
                    ],
                    borderWidth: 1,
                    borderColor: '#aa3c2c',
                    backgroundColor: '#aa3c2c',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        document.getElementById('salesMonth').addEventListener('change', handleSalesMonthSelection);


        /* Money Chart */
        function updateMoneyChart() {
            moneyChart.data.labels = labels;
            moneyChart.data.datasets[0].label = (selectedMonth === 0 ? 'Faturação de 2023 (€)' : 'Faturação de ' + months[
                selectedMonth -
                1] + ' - 2023 (€)');


            switch (selectedMonth) {
                case 0:
                    moneyChart.data.datasets[0].data = [
                        {{ $sales[0]->count() }}, {{ $sales[1]->count() }}, {{ $sales[2]->count() }},
                        {{ $sales[3]->count() }},
                        {{ $sales[4]->count() }}, {{ $sales[5]->count() }}, {{ $sales[6]->count() }},
                        {{ $sales[7]->count() }},
                        {{ $sales[8]->count() }}, {{ $sales[9]->count() }}, {{ $sales[10]->count() }},
                        {{ $sales[11]->count() }}
                    ];
                    break;
                case 1:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($janeiroMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 2:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($fevereiroMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 3:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($marcoMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 4:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($abrilMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 5:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($maioMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 6:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($junhoMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 7:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($julhoMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 8:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($agostoMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 9:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($setembroMoney as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 10:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($outubro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 11:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($novembro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
                case 12:
                    moneyChart.data.datasets[0].data = [
                        @foreach ($dezembro as $daySales)
                            {{ $daySales }},
                        @endforeach
                    ];
                    break;
            }

            moneyChart.update();
        }

        const ctx2 = document.getElementById('moneyChart');
        moneyChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Faturação de 2023 (€)',
                    data: [
                        {{ $sales[0]->sum('total') }}, {{ $sales[1]->sum('total') }},
                        {{ $sales[2]->sum('total') }},
                        {{ $sales[3]->sum('total') }}, {{ $sales[4]->sum('total') }},
                        {{ $sales[5]->sum('total') }},
                        {{ $sales[6]->sum('total') }}, {{ $sales[7]->sum('total') }},
                        {{ $sales[8]->sum('total') }},
                        {{ $sales[9]->sum('total') }}, {{ $sales[10]->sum('total') }},
                        {{ $sales[11]->sum('total') }}
                    ],
                    borderWidth: 3,
                    borderColor: '#aa3c2c',
                    backgroundColor: '#aa3c2c',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        document.getElementById('moneyMonth').addEventListener('change', handleMoneyMonthSelection);
    </script>
@endsection
