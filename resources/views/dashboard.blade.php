@section('dashboard')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <div class="square">
                    <span class="item">Products Quantity
                        <b>{{ $totalProducts }}</b></span>

                </div>
            </div>
            <div class="col-md-3">
                <div class="square">
                    <span class="item">Clients Quantity
                        <b>{{ $totalClients }}</b></span>

                </div>
            </div>
            <div class="col-md-3">
                <div class="square">
                    <span class="item">Order Quantity
                        <b>{{ $totalOrders }}</b></span>

                </div>
            </div>
            <div class="col-md-3">
                <div class="square">
                    <span class="item">Total Profit
                        <b>{{ $totalProfit }}</b></span>

                </div>
            </div>
        </div>
    </div>
    <style>
        .square {
            width: 100%;
            padding-top: 40%;
            text-align: center;
            display: flex;
            text-align: center;
            justify-content: center;
            margin-top: -20%;
            border-radius: 5%;
            border-top-left-radius: 5px;
            background-color: #696cff29;
        }

        .item {
            color: #696CFf;
            font-size: 25px;
            position: relative;
            text-align: center;
            bottom: 200%;
            transform: translateY(-150%);
        }
    </style>
@endsection
