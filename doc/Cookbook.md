# Shop Api Plugin
The Shop Api plugin is a plugin for the Sylius Ecommerce Platform which provides an easy integration for exposing the Sylius functionality to the end customer.

## Shop Api vs. Admin Api
In the default implementation of the Sylius Solution, there is already an API implemented that provides a lot of features. However, this API is more geared towards integrating other closed systems like a warehouse management system. The reason behind that is that for the Api you need to have to exchange tokens to authenticate a new client for usage. In the Shop Api everyone can log into Sylius who has an account, no token exchange necessary.

## How does the Shop Api work
<img src="Workflow.png" alt="Apis Workflow" />
The general approach that Shop Api takes is that every request is validated by the `CommandProvider` and then converted into a command by this class. Then the command is either handled directly in the `Controller` and passed to the `ViewRepository` which returns a view or by a `MessageHandler`.

### The Components
**Request**: ShopApi has its own request object. This object should abstract away the HTTP Request to a more general request type. Furthermore, it also acts as an object that can be validated as all validation rules are defined for the request objects only (commands are not validated)

**Command**: The command class is an implementation agnostic class that holds the relevant data for handling the command.

**Handler**: The handler is the class that defines the logic of what happens when a certain command is called. Here we have the business logic.

**ViewFactory**: The view factories are responsible for converting the entities into views.

**Views**: Views are primitive object that only hold scalars or other View objects which can be easily serialzied.

**ViewRepository**: ViewRepositories are repositories that return view objects from the entities they are fetched.

> ViewRepositories are **not** repositories of views.

### Command - Handler Structure
Command handling has multiple parts to it. When dispatching a command, the `MessageBus` looks for a handler that has an `__invoke` method with the parameter type that matches the type of the command that was dispatched. Before and after the handler is executed, there is a way for a "Middleware" to be executed (see below). The CommandHandler itself, however, doesn't return anything (this is not a technical limitation that is just the convention we chose in ShopApi).

> Important: The entities that were loaded from the `EntityManager` and changed in the `CommandHandler` are flushed in the Middleware. More information about the Middleware [here](https://symfony.com/doc/current/components/messenger.html#bus).

## Extending Shop Api

### Extending requests / commands
The easiest way to extend a Request object and add new properties is to extend from the Shop Api Object. There you need to change the constructor to extract the additional infomration from the request and then also override the corresponding command class. For example adding a locale to the coupon:

```php
use Sylius\ShopApiPlugin\Request\Cart\AddCouponRequest;

final class AddLocalizedCouponRequest extends AddCouponRequest
{
    /** @var string */
    protected $locale;

    protected function __construct(Request $request){
        parent::__construct($request);

        $this->locale = $request->getLocale();
    }

    public function getCommand(): CommandInterface
    {
        return new AddLocalizedCoupon($this->token, $this->coupon, $this->locale);
    }
}
```
In the same manner the command would need to be created and extending from the `AddCoupon` class. After that the only thing left to do is to is change the request class that is used in the `AddCouponAction` which can be done with configuting the correct request class in the `sylius_shop_api.yml` in the `config` directory.

### Extending handlers
The main way to extend a handler is to wrap it. This makes it easy to add functionality before and after the handler without the need to change anything. However, if you want to change the logic in the handler, you need to overwrite it. This can be done by registering the new handler with the same service id. **Do not just add it with a new service id** otherwise, it will execute both handlers.

### Extending views
When extending the views, three places need to be modified, the view class, the view factory, and the view repository (if there are any). The view class is a configuration that can be modified in the `sylius_shop_api.yml` file under the `config` folder. Which in turn gets copied over to a container parameter by the `DependencyInjection/Configuration.php` file where also the request classes are registered.

The ViewFactories, as well as the ViewRepositories, can be either completely replaced or the preferred way of doing it if you only want to add features to it, decorate the classes.

### Custom Channel Handlers
The default way that Sylius tries to resolve channels is through the hostname. However, if you want to add your own way of resolving a channel, for example from a route parameter, you need to do two things:

1. Create a class that resolves the URL to a channel

```php
class RequestAttributeChannelContext implements ChannelContext
{
    private $channelRepository;
    private $requestStack;

    public function __construct(ChannelRepositoryInterface $channelRepository, RequestStack $requestStack)
    {
        $this->channelRepository = $channelRepository;
        $this->requestStack = $requestStack
    }

    public function getChannel(): ChannelInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        try {
            return $this->channelRepository->findOneByCode($request->attributes->get('channelCode'));
        } catch(Exception $e) {
            throw new ChannelNotFoundException();
        }
    }
}
```

2. Register the class as a channel context
```xml
<service id="sylius.context.channel.request_attribute_based" class="RequestAttributeChannelContext">
    <argument type="service" id="sylius.repository.channel" />
    <argument type="service" id="request_stack" />

    <tag name="sylius.context.channel" priority="100" />
</service>
```
