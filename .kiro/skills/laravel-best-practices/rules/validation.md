# Validation & Forms Best Practices

## Validate in Controllers

Use `$request->validate()` directly in controller methods. Do not create Form Request classes.

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => ['required', 'max:255'],
        'body' => ['required'],
    ]);

    Post::create($validated);
}
```

## Array vs. String Notation for Rules

Array syntax is more readable and composes cleanly with `Rule::` objects. Prefer it in new code, but check existing controllers first and match whatever notation the project already uses.

```php
// Preferred for new code
'email' => ['required', 'email', Rule::unique('users')],

// Follow existing convention if the project uses string notation
'email' => 'required|email|unique:users',
```

## Use Only Validated Data

Get only validated data from `$request->validate()`. Never use `$request->all()` for mass operations.

Incorrect:
```php
Post::create($request->all());
```

Correct:
```php
$validated = $request->validate([
    'title' => ['required', 'max:255'],
    'body' => ['required'],
]);

Post::create($validated);
```

## Use `Rule::when()` for Conditional Validation

```php
$validated = $request->validate([
    'company_name' => [
        Rule::when($request->account_type === 'business', ['required', 'string', 'max:255']),
    ],
]);
```

## Custom Validation Across Multiple Fields

Use `Validator::make()` with an `after()` callback when validation depends on multiple fields.

```php
$validator = Validator::make($request->all(), [
    'quantity' => ['required', 'integer'],
    'product_id' => ['required', 'exists:products,id'],
]);

$validator->after(function (Validator $validator) use ($request) {
    if ($request->quantity > Product::find($request->product_id)?->stock) {
        $validator->errors()->add('quantity', 'Not enough stock.');
    }
});

$validated = $validator->validate();
```
