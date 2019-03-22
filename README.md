Entire CRUD system in as little as three lines.

In your **model**:


```php
use Illuminate\Database\Eloquent\Model;
use ctbuh\Admin\CrudResourceTrait;

class User extends Model
{
    use CrudResourceTrait; // required
    
    public function fields($context){
        // optional - will be automatically generated based on Schema.
    }
}
```

in your **controller**:

```php
use App\Http\Controllers\Controller;
use ctbuh\Admin\Traits\HasCrudActions;

class UserController extends Controller
{
    use HasCrudActions; // required
    
    protected $resource = "App\Models\User"; // required
}
```

is managed by either:

## ctbuh\Admin\Grid

Grid::fromResource("App\Models\Package");

## ctbuh\Admin\Form

Form::edit("App\Models\Package", 45);


### Terminology

- Resource/Data Model/Model
    - interchangeable and all refer to same thing which is almost always an instance to Eloquent model.

- Field/Column
    - are the same thing -> depending on the context on term will be used.

- Context
    - which "CRUD state" is the form currently in. E.g index, store, create, etc...

## ctbuh\Admin\Field

You will mostly be dealing with these properties which are all `public`

- `name` - required, and will never be empty. Can be attribute name, 
- `label` - Will default to reverse snakecase
- `resource` - model model to which this field belongs to

Methods:

- `setValue($data_model)`
    
- `formatValueUsing($callback)`
    - This will extract the value from the data model that initially fills.
    - Defaults to $data_model->{$field->name};
    
- `formatGridValueUsing($callback)`



# Field Types

Defined inside fields() method.

## Text

**Text::make($attribute_name, $label)**

Is dealing with basic attributes.

```php
Text::make('first_name');
Text::make('last_name', 'Surname');
Text::make('email');
```

label is optional. Value will resolve to the attribute name that belongs to that model.


## Select

- `options`

Appear as either a dropdown list, a radio button list, or a checkbox list when multiple=true  

- `multiple($boolean)`
- `setOptions($data)` - either an array or Eloquent instance that will be queried.
- `placeholder($text)` - will add an additional OPTION with value=null, and text=$text
- `setOptionsUsing($callback|string)`
- `queryOptionsUsing($builder)` - called if $data is an eloquent Model

TODO:
- `asEnum()`
- `asDropdownList()` -- default
- `asRadioList()`
- `asCheckboxList()` -- default when multiple=true


**Examples**

```php
Select::make('salutation')->setOptions(array(
    'Mr.' => 'Mr.',
    'Mrs.' => 'Mrs.',
    'Ms.' => 'Ms.'
))->placeholder('Select title... ');

Select::make('language')->multiple(true)->setOptions(array(
    'en' => 'English',
    'cn' => 'Chinese',
));

// resolve on demand
Select::make('country_id', 'Select Country')->setOptions(Country::class);
Select::make('country_id', 'Country')->getOptions("App\Models\Country");
```

**index/show**:

Either a plain string or JSON string if multiple=true

    $resource->{$field->name}
    
**store/update**:
    
    // 'Mr.'
    $resource->salutation = request()->get('salutation');

when multiple=true

    // "{['en', 'cn']}"
    $resource->language = json_encode(request()->get('language'));


Most commonly used with **relations**.

## BelongsTo (extends Select)


```php
BelongsTo::make('user_id', 'Select User')->setOptionsUsing(function($data){
    return $data->pluck('name', 'id'); // default
});

BelongsTo::make('registrant_id')->queryOptionsUsing(function($builder){
    
    $builder->select('id', 'email')->whereNull('group_id');
    
})->setOptionsUsing('id:email'); // value:text

// TODO: convert to Select2
BelongsTo::make('person_id')->ajax('/api/people');
```

getValue()

    $resource->user_id
    
gridValue():

    $resource->user()->label()
    $resource->user_id

Submit Logic:

```php
$resource->user_id = request()->get('user_id');
```


## BelongsToMany (extends Select)

```php
BelongsToMany::make('topics');

// TODO:
BelongsToMake::make('companies')->withPivotFields(function($resource){
    return array(
        Text::make('company_name'),
        Text::make('company_name_chinese')
    )
});
```

Will parse out the apropriate model to query from TOPICS relationship.

value():

    $resource->topics | Collection
    
**index** / gridValue():

    $resource->topics[].label
    

Context: **store/update**:
    
    $resource->topics->sync(request()->get('topic_id'))
    
    //  TODO: pivotSyncing
    

## HasMany

Introduces three new fields.

- `withFields($closure)`
- `getRelatedFields()`
- `getRelatedResource()`

**examples:**

```php
HasMany::make('keywords');

HasMany::make('keywords')->withFields(function($data){
    return array(
        Text::make('keyword')
    );
});
```

**create/edit**:

Not visible on 'create'. model has to be created first, before those can be edited. 
Always `Collection`:

    $resource->keywords
    
**index**:

    $resource->keywords[].label


## Why another one?

faults:
https://github.com/z-song/demo.laravel-admin.org/blob/master/app/Admin/Controllers/LightboxController.php

too much duplicate code. have to reefine columns for boht eit/update
bootstrap specific
too much

nova:
vue-based


## TODO

https://github.com/nsaumini/nova-field-count/blob/master/src/RelationshipCount.php

