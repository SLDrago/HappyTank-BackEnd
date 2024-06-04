<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    @if (isset($fishes))
        <form action="/test/testfish" method="post">
            @csrf
            <select name="fish1" id="fish1">
                @foreach ($fishes as $fish)
                    <option value="{{ $fish->id }}">{{ $fish->Common_Name }}</option>
                @endforeach
            </select>
            <select name="fish2" id="fish2">
                @foreach ($fishes as $fish)
                    <option value="{{ $fish->id }}">{{ $fish->Common_Name }}</option>
                @endforeach
            </select>
            <select name="fish3" id="fish3">
                @foreach ($fishes as $fish)
                    <option value="{{ $fish->id }}">{{ $fish->Common_Name }}</option>
                @endforeach
            </select>
            <button type="submit">Submit</button>
        </form>
    @endif

    @if (isset($selectedFish1, $selectedFish2, $selectedFish3))
        <h2>Selected Fish Details:</h2>
        <h3>Fish 1:</h3>
        <ul>
            @foreach ($selectedFish1->getAttributes() as $key => $value)
                <li>{{ $key }}: {{ $value }}</li>
            @endforeach
        </ul>
        <h3>Fish 2:</h3>
        <ul>
            @foreach ($selectedFish2->getAttributes() as $key => $value)
                <li>{{ $key }}: {{ $value }}</li>
            @endforeach
        </ul>
        <h3>Fish 3:</h3>
        <ul>
            @foreach ($selectedFish3->getAttributes() as $key => $value)
                <li>{{ $key }}: {{ $value }}</li>
            @endforeach
        </ul>
    @endif
</body>

</html>
