# ProductDetails

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** |  | [optional] 
**name** | **string** |  | [optional] 
**slug** | **string** |  | [optional] 
**average_rating** | **int** |  | [optional] 
**taxons** | [**\Swagger\Client\Model\ProductTaxon**](ProductTaxon.md) |  | [optional] 
**variants** | [**map[string,\Swagger\Client\Model\Variant[]]**](array.md) | Keys reference to code of a variant. | [optional] 
**attributes** | [**\Swagger\Client\Model\Attribute[]**](Attribute.md) |  | [optional] 
**associations** | [**map[string,\Swagger\Client\Model\Product[]]**](array.md) | Keys reference to code of an association. | [optional] 
**images** | [**\Swagger\Client\Model\Image[]**](Image.md) |  | [optional] 
**breadcrumb** | **string** |  | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


