@props([
    'name',
])

@error($name)
    <p {{ $attributes->class(['error']) }}>{{ $message }}</p>
@enderror
