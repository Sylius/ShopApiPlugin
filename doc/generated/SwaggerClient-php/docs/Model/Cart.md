# Cart

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**token_value** | **string** |  | [optional] 
**channel** | **string** |  | [optional] 
**currency** | **string** | Code of the cart currency according to ISO 4217. This value is inherited from channel | [optional] 
**locale** | **string** | Code of the cart locale. This value is inherited from channel | [optional] 
**checkout_state** | **string** | Current state of a checkout. | [optional] [default to 'cart']
**items** | [**\Swagger\Client\Model\CartItem[]**](CartItem.md) |  | [optional] 
**totals** | [**\Swagger\Client\Model\TotalsView**](TotalsView.md) |  | [optional] 
**shipping_address** | [**\Swagger\Client\Model\Address**](Address.md) |  | [optional] 
**billing_address** | [**\Swagger\Client\Model\Address**](Address.md) |  | [optional] 
**payments** | [**\Swagger\Client\Model\Payment[]**](Payment.md) |  | [optional] 
**shipments** | [**\Swagger\Client\Model\Shipment[]**](Shipment.md) |  | [optional] 
**cart_discounts** | [**\Swagger\Client\Model\CartCartDiscounts[]**](CartCartDiscounts.md) |  | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


