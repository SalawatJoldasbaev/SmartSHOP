<table>
    <thead>
    <tr>
        <th>CODE</th>
        <th>CATEGORY</th>
        <th>PRODUCT NAME</th>
        <th>BRAND</th>
        <th>COST PRICE</th>
        <th>COST PRICE CURRENCY</th>
        <th>WHOLESALE PRICE</th>
        <th>WHOLESALE PRICE CURRENCY</th>
        <th>PRICE MAX</th>
        <th>PRICE MAX CURRENCY</th>
        <th>PRICE MIN</th>
        <th>PRICE MIN CURRENCY</th>
        <th>WAREHOUSE COUNT</th>
        <th>UUID</th>
        <th>IMAGE</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>#{{ $product['id'] }}</td>
            <td>{{ $product['category']['name'] }}</td>
            <td>{{ $product['name'] }}</td>
            <td>{{ $product['brand'] }}</td>
            <td>{{ $product['cost_price']['price'] }}</td>
            <td>{{ $product['cost_price']['code'] }}</td>

            <td>{{ $product['whole_price']['price'] }}</td>
            <td>{{ $product['whole_price']['code'] }}</td>

            <td>{{ $product['max_price']['price'] }}</td>
            <td>{{ $product['max_price']['code'] }}</td>

            <td>{{ $product['min_price']['price'] }}</td>
            <td>{{ $product['min_price']['code'] }}</td>
            <td>{{$product['warehouse']['count'] ?? 0}}</td>
            <td>{{$product['uuid'] ?? ''}}</td>
            <td>{{$product['image'] ?? ''}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
