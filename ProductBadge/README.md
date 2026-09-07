# Codilar Product Badge

A Magento 2 module that adds a custom product badge to product cards.

## Features

- Adds a custom `Product Badge` attribute to products.
- Provides the following badge options:
    - No Badge
    - New
    - Sale
    - Out of Stock
- Each product can have its own badge.
- Only one badge is displayed for each product.
- Badge is displayed on:
    - Product Details Page (PDP)
    - Product Listing Page (PLP)
    - Search Listing Page (SLP)
- Badge content is controlled by the product attribute value.
- Includes separate styling for each badge type.

## How It Works

The `Product Badge` attribute is added to the product configuration.

When creating or editing a product, the admin can select one badge option.

The selected badge is then displayed on the product card.

If `No Badge` is selected, no badge will be displayed.

## Badge Types

| Badge | Description |
|---|---|
| No Badge | No badge is displayed |
| New | Displays a New badge |
| Sale | Displays a Sale badge |
| Out of Stock | Displays an Out of Stock badge |

## Module Structure

- `Block/` - Contains the badge block logic.
- `Plugin/` - Adds the badge to product listing output.
- `Setup/Patch/Data/` - Creates and assigns the product badge attribute.
- `view/frontend/templates/` - Contains the badge template.
- `view/frontend/web/css/` - Contains badge styling.

## Module Information

**Vendor:** Codilar  
**Module:** ProductBadge  
**Namespace:** `Codilar\ProductBadge`
