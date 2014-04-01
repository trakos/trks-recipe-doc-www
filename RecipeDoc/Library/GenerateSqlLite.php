<?php

namespace RecipeDoc\Library;


use RecipeDoc\Model\RecipeDocData;

class GenerateSqlLite
{
    static $adapter;
    static $sql;

    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    static function getAdapter()
    {
        return self::$adapter ? : self::$adapter = new \Zend\Db\Adapter\Adapter(array(
            'driver'   => 'Pdo_Sqlite',
            'database' => 'C:/test/sqlite.db'
        ));
    }

    /**
     * @return \Zend\Db\Sql\Sql
     */
    static function getSql()
    {
        return self::$sql ? : self::$sql = new \Zend\Db\Sql\Sql(self::getAdapter());
    }

    /**
     * @param $name
     * @param $values
     * @return int
     */
    static function insert($name, $values)
    {
        self::getSql()->prepareStatementForSqlObject(self::getSql()->insert($name)->values($values))->execute();
        return self::getAdapter()->getDriver()->getLastGeneratedValue();
    }

    /**
     * @param $string
     * @param $array
     * @return \Zend\Db\Adapter\Driver\StatementInterface|\Zend\Db\ResultSet\ResultSet
     */
    private static function fetchAll($string, $array)
    {
        return self::getAdapter()->query($string, $array);
    }

    static public function go()
    {
        set_time_limit(0);
        self::fillCategories();
        self::fillItems();
        self::fillRecipes();
        self::fillHandlers();
    }

    static function fillCategories()
    {
        self::getAdapter()->query("DROP TABLE IF EXISTS category")->execute();
        self::getAdapter()->query("CREATE TABLE category
        (
            category_name text NOT NULL PRIMARY KEY
        )")->execute();

        foreach (RecipeDocData::getInstance()->getData()->itemCategories as $itemCategory) {
            self::insert('category', array(
                'category_name' => $itemCategory
            ));
        }
    }

    static function fillItems()
    {

        self::getAdapter()->query("DROP TABLE IF EXISTS item")->execute();
        self::getAdapter()->query("CREATE TABLE item
        (
            item_id int NOT NULL,
            item_damage int NOT NULL,
            item_icon text NOT NULL,
            item_name text NOT NULL,
            item_rawName text NOT NULL,
            item_mod text NOT NULL,
            item_type text NOT NULL,
            item_category_name text NOT NULL,
            item_description text NOT NULL,
            item_showOnList int NOT NULL,
            item_isBaseItem int NOT NULL,
            PRIMARY KEY ( item_id, item_damage )
        )")->execute();

        self::getAdapter()->query("DROP TABLE IF EXISTS item_attribute")->execute();
        self::getAdapter()->query("CREATE TABLE item_attribute
        (
            attribute_item_id int NOT NULL,
            attribute_damage_id int NOT NULL,
            attribute_name text NOT NULL,
            attribute_value text NOT NULL,
            PRIMARY KEY ( attribute_item_id, attribute_damage_id, attribute_name )
        )")->execute();

        self::getAdapter()->query("DROP TABLE IF EXISTS item_rawcost")->execute();
        self::getAdapter()->query("CREATE TABLE item_rawcost
        (
            rawcost_id integer PRIMARY KEY AUTOINCREMENT NOT NULL,
            rawcost_item_id int NOT NULL,
            rawcost_damage_id int NOT NULL
        );")->execute();

        self::getAdapter()->query("DROP TABLE IF EXISTS item_rawcost_entry")->execute();
        self::getAdapter()->query("CREATE TABLE item_rawcost_entry
        (
            entry_rawcost_id int NOT NULL,
            entry_item_id int NOT NULL,
            entry_item_damage int NOT NULL,
            entry_amount real NOT NULL,
            PRIMARY KEY ( entry_rawcost_id, entry_item_id, entry_item_damage )
        )")->execute();

        foreach (RecipeDocData::getInstance()->getData()->items as $item) {

            if (self::fetchAll("SELECT * FROM item WHERE item_id = ? AND item_damage = ?", array($item->id, $item->damage))->current()) {
                continue;
            }
            self::insert('item', array(
                'item_id'            => $item->id,
                'item_damage'        => $item->damage,
                'item_icon'          => $item->icon,
                'item_name'          => $item->name,
                'item_rawName'       => $item->rawName,
                'item_mod'           => $item->mod,
                'item_type'          => $item->type,
                'item_category_name' => $item->category,
                'item_description'   => $item->description,
                'item_showOnList'    => $item->showOnList,
                'item_isBaseItem'    => $item->isBaseItem
            ));

            foreach ($item->attributes as $attribute) {
                self::insert('item_attribute', array(
                    'attribute_item_id'   => $item->id,
                    'attribute_damage_id' => $item->damage,
                    'attribute_name'      => $attribute['id'],
                    'attribute_value'     => $attribute['value']
                ));
            }

            foreach ($item->rawCost as $rawCost) {
                $id = self::insert('item_rawcost', array(
                    'rawcost_item_id'   => $item->id,
                    'rawcost_damage_id' => $item->damage
                ));

                foreach ($rawCost->items as $rawCostItem) {
                    self::insert('item_rawcost_entry', array(
                        'entry_rawcost_id'  => $id,
                        'entry_item_id'     => $rawCostItem->id,
                        'entry_item_damage' => $rawCostItem->damage,
                        'entry_amount'      => $rawCostItem->amount
                    ));
                }
            }
        }
    }

    static function fillRecipes()
    {
        self::getAdapter()->query("DROP TABLE IF EXISTS recipe")->execute();
        self::getAdapter()->query("CREATE TABLE recipe
        (
            recipe_id integer PRIMARY KEY AUTOINCREMENT NOT NULL,
            recipe_handler_id text NOT NULL,
            recipe_visible int NOT NULL
        )")->execute();

        self::getAdapter()->query("DROP TABLE IF EXISTS recipe_ingredient")->execute();
        self::getAdapter()->query("CREATE TABLE recipe_ingredient
        (
            ingredient_id integer PRIMARY KEY AUTOINCREMENT NOT NULL,
            ingredient_recipe_id NOT NULL,
            ingredient_type text NOT NULL,
            ingredient_x int NOT NULL,
            ingredient_y int NOT NULL,
            ingredient_amount real NOT NULL
        )")->execute();

        self::getAdapter()->query("DROP TABLE IF EXISTS recipe_ingredient_option")->execute();
        self::getAdapter()->query("CREATE TABLE recipe_ingredient_option
        (
            option_ingredient_id int NOT NULL,
            option_item_id int NOT NULL,
            option_item_damage int NOT NULL,
            PRIMARY KEY ( option_ingredient_id, option_item_damage, option_item_id )
        )")->execute();

        foreach (RecipeDocData::getInstance()->getData()->recipes as $recipe) {
            $id = self::insert('recipe', array(
                'recipe_handler_id' => $recipe->recipeHandler,
                'recipe_visible'    => $recipe->visible
            ));

            foreach ($recipe->ingredients as $ingredient) {
                $ingredientId = self::insert('recipe_ingredient', array(
                    'ingredient_recipe_id' => $id,
                    'ingredient_type'      => $ingredient->type,
                    'ingredient_x'         => $ingredient->x,
                    'ingredient_y'         => $ingredient->y,
                    'ingredient_amount'    => $ingredient->amount
                ));

                foreach ($ingredient->items as $item) {
                    if (self::fetchAll("SELECT * FROM recipe_ingredient_option WHERE option_ingredient_id = ? AND option_item_id = ? AND option_item_damage = ?", array($ingredientId, $item->id, $item->damage))->current()) {
                        continue;
                    }
                    self::insert('recipe_ingredient_option', array(
                        'option_ingredient_id' => $ingredientId,
                        'option_item_id'       => $item->id,
                        'option_item_damage'   => $item->damage
                    ));
                }
            }
        }
    }

    static function fillHandlers()
    {
        self::getAdapter()->query("DROP TABLE IF EXISTS handler")->execute();
        self::getAdapter()->query("CREATE TABLE handler
        (
            handler_id text NOT NULL PRIMARY KEY,
            handler_name text NOT NULL,
            handler_image text NOT NULL
        );")->execute();

        self::getAdapter()->query("DROP TABLE IF EXISTS handler_machine")->execute();
        self::getAdapter()->query("CREATE TABLE handler_machine
        (
            machine_handler_id text NOT NULL,
            machine_item_id int NOT NULL,
            machine_item_damage int NOT NULL,
            PRIMARY KEY ( machine_handler_id, machine_item_id, machine_item_damage )
        )")->execute();

        foreach (RecipeDocData::getInstance()->getData()->recipeTypes as $recipeType) {
            self::insert('handler', array(
                'handler_id'    => $recipeType->id,
                'handler_name'  => $recipeType->name,
                'handler_image' => $recipeType->image
            ));

            foreach ($recipeType->machines as $machine) {
                self::insert('handler_machine', array(
                    'machine_handler_id'  => $recipeType->id,
                    'machine_item_id'     => $machine->id,
                    'machine_item_damage' => $machine->damage,
                ));
            }
        }
    }

} 