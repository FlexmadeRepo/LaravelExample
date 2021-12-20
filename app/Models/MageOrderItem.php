<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MageOrderItem extends Model
{
    use HasFactory;

    protected $casts = [
        'product_option' => 'array',
        'applied_rule_ids' => 'array',
        'json' => 'array'
    ];


    protected $fillable = [
        "sku",
        "name",
        "price",
        "weight",
        "item_id",
        "order_id",
        "store_id",
        "row_total",
        "base_price",
        "created_at",
        "product_id",
        "row_weight",
        "tax_amount",
        "updated_at",
        "no_discount",
        "qty_ordered",
        "qty_shipped",
        "tax_percent",
        "product_type",
        "qty_canceled",
        "qty_invoiced",
        "qty_refunded",
        "row_invoiced",
        "tax_invoiced",
        "free_shipping",
        "base_row_total",
        "is_qty_decimal",
        "original_price",
        "price_incl_tax",
        "product_option",
        "amount_refunded",
        "base_tax_amount",
        "discount_amount",
        "applied_rule_ids",
        "discount_percent",
        "base_row_invoiced",
        "base_tax_invoiced",
        "discount_invoiced",
        "row_total_incl_tax",
        "base_original_price",
        "base_price_incl_tax",
        "base_amount_refunded",
        "base_discount_amount",
        "base_discount_invoiced",
        "base_row_total_incl_tax",
        "discount_tax_compensation_amount",
        "discount_tax_compensation_invoiced",
        "base_discount_tax_compensation_amount",
        "base_discount_tax_compensation_invoiced"
    ];
}
