<?php

namespace RecipeDoc\Model;


use ZendService\ReCaptcha\Exception;

class RecipeDocData
{
    const RECIPE_ITEM_TYPE_INGREDIENT = "ingredient";
    const RECIPE_ITEM_TYPE_OTHER = "other";
    const RECIPE_ITEM_TYPE_RESULT = "result";

    /**
     * @var RecipeDocData
     */
    static protected $instance;

    /**
     * @return RecipeDocData
     */
    public static function getInstance()
    {
        return self::$instance ? : (self::$instance = new RecipeDocData());
    }

    /**
     * @var \RecipeDocData
     */
    protected $recipeDocData = null;
    protected $modList = [];
    protected $itemTypeList = [];
    protected $itemTree = [];

    public function __construct()
    {
        /* @var \RecipeDocData $recipeDocData */
        $recipeDocData = require T_PATH_DATA . "/recipes.php";

        foreach ($recipeDocData->items as $item)
        {
            if (array_search($item->mod, $this->modList) === false) {
                $this->modList[] = $item->mod;
            }

            if (array_search($item->type, $this->itemTypeList) === false) {
                $this->itemTypeList[] = $item->type;

                $tree = $this->itemTree;
                $path = explode(".", $item->type);
                foreach ($path as $breadcrumb)
                {
                    if (!isset($tree[$breadcrumb])) $tree[$breadcrumb] = [];
                    $tree = $tree[$breadcrumb];
                }
                $tree[] = $item;
            }
        }

        $this->recipeDocData = $recipeDocData;
    }

    /**
     * @return \RecipeDocData
     */
    public function getData()
    {
        return $this->recipeDocData;
    }

    /**
     * @return string[]
     */
    public function getModList()
    {
       sort($this->modList);
       return $this->modList;
    }

    /**
     * @return string[]
     */
    public function getItemTypeList()
    {
        return $this->itemTypeList;
    }

    /**
     * @return string[]
     */
    public function getItemTree()
    {
        return $this->itemTypeList;
    }

    /**
     * @param $itemId
     * @param $damage
     *
     * @return \Item|null
     */
    public function getItem($itemId, $damage)
    {
        foreach ($this->recipeDocData->items as $item)
        {
            if ($item->id == $itemId && $item->damage == $damage)
            {
                return $item;
            }
        }
        return null;
    }

    public function getItems($itemType = null, $mod = null)
    {
        $items = [];
        foreach ($this->recipeDocData->items as $item)
        {
            if (
                ($itemType == null || $item->type == $itemType || $this->isItemOfType($item, $itemType))
                && ($mod == null || $item->mod == $mod))
            {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Grouped by mod, category
     */
    public function getGroupedItems()
    {
        $groupedByMod = [];
        foreach ($this->getModList() as $mod)
        {
            $data = [];
            foreach ($this->recipeDocData->itemCategories as $category)
            {
                $data[$category] = [];
            }
            foreach ($this->recipeDocData->items as $item)
            {
                if ($item->mod != $mod || !$item->showOnList) continue;
                $data[$item->category][] = $item;
            }
            $nonEmpties = [];
            foreach ($data as $category => $items)
            {
                if (!empty($data[$category])) {
                    $nonEmpties[$category] = $items;
                }
            }
            $groupedByMod[$mod] = $nonEmpties;
        }
        return $groupedByMod;
    }

    /**
     * @param \Item $item
     * @param string $type
     * @return bool
     */
    protected function isItemOfType(\Item $item, $type)
    {
        return substr($item->type, 0, strlen($type)) == $type;
    }

    /**
     * @param string $itemId
     * @param string $damage
     * @param string[] $types
     * @return \Recipe[]
     */
    public function getRecipesWhereItemIs($itemId, $damage, $types)
    {
        $recipes = [];
        foreach ($this->recipeDocData->recipes as $recipe)
        {
            $found = false;
            foreach ($recipe->ingredients as $ingredient)
            {
                if (array_search($ingredient->type, $types) === false)
                {
                    continue;
                }
                foreach ($ingredient->items as $item)
                {
                    if ($item->damage == $damage && $item->id == $itemId)
                    {
                        $found = true;
                        break;
                    }
                }
                if ($found)
                {
                    break;
                }
            }
            if ($found)
            {
                $recipes[] = $recipe;
            }
        }
        return $recipes;
    }

    /**
     * @param string $itemId
     * @param string $damage
     * @return \Recipe[]
     */
    public function getRecipesWhereItemIsIngredient($itemId, $damage)
    {
        return $this->getRecipesWhereItemIs($itemId, $damage, array(self::RECIPE_ITEM_TYPE_INGREDIENT));
    }

    /**
     * @param string $itemId
     * @param string $damage
     * @return \Recipe[]
     */
    public function getRecipesWhereItemIsResult($itemId, $damage)
    {
        return $this->getRecipesWhereItemIs($itemId, $damage, array(self::RECIPE_ITEM_TYPE_RESULT));
    }

    /**
     * @param string $itemId
     * @param string $damage
     * @return \Recipe[]
     */
    public function getRecipesWhereItemIsOther($itemId, $damage)
    {
        return $this->getRecipesWhereItemIs($itemId, $damage, array(self::RECIPE_ITEM_TYPE_OTHER));
    }

    /**
     * @param $recipeHandlerId
     * @return \Recipe[]
     */
    public function getRecipesByCraftingType($recipeHandlerId)
    {
        $recipes = [];
        foreach ($this->recipeDocData->recipes as $recipe)
        {
            if ($recipe->recipeHandler == $recipeHandlerId)
            {
                $recipes[] = $recipe;
            }
        }
        return $recipes;
    }

    /**
     * @param $itemId
     * @param $damageId
     * @return \RecipeHandler[]
     */
    public function getRecipeHandlersByMachineItem($itemId, $damageId)
    {
        $recipeHandlers = [];
        foreach ($this->recipeDocData->recipeTypes as $recipeType)
        {
            $found = false;
            foreach ($recipeType->machines as $machine)
            {
                if ($machine->id == $itemId && $machine->damage == $damageId)
                {
                    $found = true;
                    break;
                }
            }
            if ($found)
            {
                $recipeHandlers[] = $recipeType;
            }
        }
        return $recipeHandlers;
    }

    /**
     * @param $recipeHandlerId
     * @return null|\RecipeHandler
     */
    public function getRecipeHandler($recipeHandlerId)
    {
        foreach ($this->recipeDocData->recipeTypes as $recipeHandler)
        {
            if ($recipeHandler->id == $recipeHandlerId)
            {
                return $recipeHandler;
            }
        }
        return null;
    }

} 