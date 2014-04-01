<?php

class RecipeDocData
{
    /**
     * @var Item[]
     */
    public $items;
    /**
     * @var Recipe[]
     */
    public $recipes;
    /**
     * @var RecipeHandler[]
     */
    public $recipeTypes;
    /**
     * @var string[]
     */
    public $itemCategories;

    public function __construct($array)
    {
        $this->items = $array['items'];
        $this->recipes = $array['recipes'];
        $this->recipeTypes = $array['recipeTypes'];
        $this->itemCategories = $array['itemCategories'];
    }
}

class Item
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $damage;
    /**
     * @var string
     */
    public $icon;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $rawName;
    /**
     * @var string
     */
    public $mod;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $category;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string[]
     */
    public $attributes;
    /**
     * @var bool
     */
    public $showOnList;
    /**
     * @var bool
     */
    public $isBaseItem;
    /**
     * @var ItemRawCost[]
     */
    public $rawCost;

    public function __construct($array)
    {
        $this->id = $array['id'];
        $this->damage = $array['damage'];
        $this->icon = $array['icon'];
        $this->name = $array['name'];
        $this->rawName = $array['rawName'];
        $this->mod = $array['mod'];
        $this->type = $array['type'];
        $this->category = $array['category'];
        $this->description = $array['description'];
        $this->attributes = $array['attributes'];
        $this->showOnList = $array['showOnList'];
        $this->isBaseItem = $array['isBaseItem'];
        $this->rawCost = $array['rawCost'];
    }
}

class ItemRawCost
{
    /**
     * @var ItemRawCostEntry[]
     */
    public $items;

    public function __construct($array)
    {
        $this->items = $array['items'];
    }
}

class ItemRawCostEntry
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $damage;
    /**
     * @var float
     */
    public $amount;

    public function __construct($array)
    {
        $this->id = $array['id'];
        $this->damage = $array['damage'];
        $this->amount = $array['amount'];
    }
}

class ItemId
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $damage;

    public function __construct($array)
    {
        $this->id = $array['id'];
        $this->damage = $array['damage'];
    }
}

class RecipeIngredient
{
    /**
     * @var string
     */
    public $type;
    /**
     * @var int
     */
    public $x;
    /**
     * @var int
     */
    public $y;
    /**
     * @var int
     */
    public $amount;
    /**
     * @var ItemId[]
     */
    public $items;

    public function __construct($array)
    {
        $this->type = $array['type'];
        $this->x = $array['x'];
        $this->y = $array['y'];
        $this->amount = $array['amount'];
        $this->items = $array['items'];
    }
}

class Recipe
{
    /**
     * @var string
     */
    public $recipeHandler;
    /**
     * @var RecipeIngredient[]
     */
    public $ingredients;
    /**
     * @var Boolean
     */
    public $visible;

    public function __construct($array)
    {
        $this->recipeHandler = $array['recipeHandler'];
        $this->ingredients = $array['ingredients'];
        $this->visible = $array['visible'];
    }
}

class RecipeHandler
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $image;
    /**
     * @var ItemId[]
     */
    public $machines;

    public function __construct($array)
    {
        $this->id = $array['id'];
        $this->name = $array['name'];
        $this->image = $array['image'];
        $this->machines = $array['machines'];
    }
}