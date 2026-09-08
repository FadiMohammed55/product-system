<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
</head>

<body>

    <h1>Products</h1>
    <p>Welcome, {{ Auth::user()->name }}</p>

    <form action="/logout" method="post">
        @csrf
        <button type="submit">
            Logout
        </button>
    </form>

    <br>

    @if ($products->isEmpty())
        <p>No products available</p>
    @else

        <table border="1" cellpadding="10">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Currency</th>
                    <th>Rating</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if ($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="100">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->currency }}</td>
                        <td>{{ $product->rating }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    @endif

</body>

</html>