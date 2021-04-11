# BixbyMarket

An Advanced GUI-market plugin for PocketMine-MP

## Commands

|command|description|usage|permission|alias|
|---|---|---|---|---|
|/mcategory|Opens the category GUI|/mcategory <category name>|bixbymarket.command.open_category|/mca|
|/categorycreate|Creates a category|/categorycreate <category name>|bixbymarket.command.category_create|/cc|
|/categoryedit|Edits a category (Edit category list)|/categoryedit <category name>|bixbymarket.command.edit_category|/ce|
|/marketedit|Edits a market category (Edit market list)|/marketedit <category name>|bixbymarket.command.edit_market|-|
|/medit|Edits a market|/medit|bixbymarket.command.edit_market|-|

## Permissions
|permission|default|
|---|---|
|bixbymarket.command.open_category|true|
|bixbymarket.command.category_create|op|
|bixbymarket.command.edit_category|op|
|bixbymarket.command.edit_market|op|
|bixbymarket.market.sellall|true|

## Installation / Setup

1. Download plugin from [Poggit CI](https://poggit.pmmp.io/ci/TeamBixby/BixbyMarket).
2. (Optional) Set up your preferred market format on [eng.ini](./resources/eng.ini).
3. Restart your server.
4. Use `/categorycreate <category name>` to create category.
5. Use `/marketedit <category name>` to register/unregister market. (If you want to add a market, just drag and drop items into chest inventory)
6. Use `/marketedit` to edit market price. (before use this command, hold the item that you want to edit price.)
7. Enjoy!

## Opens Market GUI

Use `/mcategory <category name>` to open Category GUI. (category name is optional)