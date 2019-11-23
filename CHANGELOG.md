# CHANGELOG FOR `1.0.X`

## v1.0.0

#### TL;DR

- Order confirmation email was added to the project.

#### Details

### Added
- [#593](https://github.com/Sylius/SyliusShopApiPlugin/issues/593) [Checkout] Send confirmation email after checkout is completed ([@lchrusciel](https://github.com/lchrusciel))
- [#599](https://github.com/Sylius/SyliusShopApiPlugin/issues/599) Validate complete order request ([@kayue](https://github.com/kayue), [@mamazu](https://github.com/mamazu))

### Changed
- [#590](https://github.com/Sylius/SyliusShopApiPlugin/issues/590) [Maintenance] Register handler only in proper bus ([@lchrusciel](https://github.com/lchrusciel))
- [#602](https://github.com/Sylius/SyliusShopApiPlugin/issues/602) Loosen dep constraint for Symfony messenger component ([@TiMESPLiNTER](https://github.com/TiMESPLiNTER))
- [#604](https://github.com/Sylius/SyliusShopApiPlugin/issues/604) Add missing branch alias ([@Zales0123](https://github.com/Zales0123))
- [#591](https://github.com/Sylius/SyliusShopApiPlugin/issues/591) [Cart] Dispatch CartPickedUp after doctrine transaction ([@lchrusciel](https://github.com/lchrusciel))

### Fixed
- [#573](https://github.com/Sylius/SyliusShopApiPlugin/issues/573) Extending customer details tests ([@mamazu](https://github.com/mamazu))
- [#605](https://github.com/Sylius/SyliusShopApiPlugin/issues/605) Update test mockups and fix the build ([@bitbager](https://github.com/bitbager))

## v1.0.0-rc.3

#### TL;DR

- Fixing several issues

#### Details

### Changed
- [#587](https://github.com/Sylius/SyliusShopApiPlugin/issues/587) [Minor] Code cleanup ([@lchrusciel](https://github.com/lchrusciel))

### Fixed
- [#568](https://github.com/Sylius/SyliusShopApiPlugin/issues/568) [Customer] Fix customer update action ([@lchrusciel](https://github.com/lchrusciel))
- [#579](https://github.com/Sylius/SyliusShopApiPlugin/issues/579) Fixing dependencies ([@mamazu](https://github.com/mamazu))
- [#578](https://github.com/Sylius/SyliusShopApiPlugin/issues/578) Fix token generator service for customer's password reset handler ([@diimpp](https://github.com/diimpp))
- [#570](https://github.com/Sylius/SyliusShopApiPlugin/issues/570) Cleanup swagger.yml from remains ChannelCode parameters ([@diimpp](https://github.com/diimpp))
- [#586](https://github.com/Sylius/SyliusShopApiPlugin/issues/586) [Product] Introduce product attribute value view resolvers ([@denis019](https://github.com/denis019), [@lchrusciel](https://github.com/lchrusciel), [@GSadee](https://github.com/GSadee))

## v1.0.0-rc.2

#### TL;DR

- Several issues were fixed and a first iteration of documentation was added.

#### Details

### Added
- [#503](https://github.com/Sylius/SyliusShopApiPlugin/issues/503) Add onHand to product response ([@alexander-schranz](https://github.com/alexander-schranz))
- [#502](https://github.com/Sylius/SyliusShopApiPlugin/issues/502) Add originalPrice to product response ([@alexander-schranz](https://github.com/alexander-schranz))
- [#514](https://github.com/Sylius/SyliusShopApiPlugin/issues/514) Adding a Cookbook ([@mamazu](https://github.com/mamazu))
- [#537](https://github.com/Sylius/SyliusShopApiPlugin/issues/537) ImageViewFactory generates product image cache ([@mamazu](https://github.com/mamazu), [@lchrusciel](https://github.com/lchrusciel))

### Changed
- [#499](https://github.com/Sylius/SyliusShopApiPlugin/issues/499) [AddressBook] Fixes for set default address action ([@GSadee](https://github.com/GSadee))
- [#500](https://github.com/Sylius/SyliusShopApiPlugin/issues/500) [Docs] Upgrade file fix ([@lchrusciel](https://github.com/lchrusciel))
- [#505](https://github.com/Sylius/SyliusShopApiPlugin/issues/505) Returning 401 on customer details/update actions when not logged in ([@JakobTolkemit](https://github.com/JakobTolkemit))
- [#507](https://github.com/Sylius/SyliusShopApiPlugin/issues/507) Fix product variant view factory original price test ([@alexander-schranz](https://github.com/alexander-schranz))
- [#504](https://github.com/Sylius/SyliusShopApiPlugin/issues/504) Moving the complete phpstan analysis to composer ([@mamazu](https://github.com/mamazu))
- [#529](https://github.com/Sylius/SyliusShopApiPlugin/issues/529) Fixing codestyle ([@mamazu](https://github.com/mamazu))
- [#512](https://github.com/Sylius/SyliusShopApiPlugin/issues/512) Revert "Add onHand to product response" ([@lchrusciel](https://github.com/lchrusciel), [@mamazu](https://github.com/mamazu))
- [#551](https://github.com/Sylius/SyliusShopApiPlugin/issues/551) Adding sylius 1.6 to the travis tests ([@mamazu](https://github.com/mamazu))
- [#550](https://github.com/Sylius/SyliusShopApiPlugin/issues/550) Making phpstan level higher ([@mamazu](https://github.com/mamazu))
- [#520](https://github.com/Sylius/SyliusShopApiPlugin/issues/520) docs(swagger): fix `/carts/{token}/multiple-items` ([@Gounlaf](https://github.com/Gounlaf), [@mamazu](https://github.com/mamazu))
- [#552](https://github.com/Sylius/SyliusShopApiPlugin/issues/552) Adding more methods to the verify account ([@mamazu](https://github.com/mamazu))
- [#526](https://github.com/Sylius/SyliusShopApiPlugin/issues/526) [CHANGELOG] Describe changes between beta.21 and rc.1 ([@lchrusciel](https://github.com/lchrusciel))
- [#553](https://github.com/Sylius/SyliusShopApiPlugin/issues/553) Adding translations and unifying keys ([@mamazu](https://github.com/mamazu))
- [#559](https://github.com/Sylius/SyliusShopApiPlugin/issues/559) [README] Minor improvements ([@lchrusciel](https://github.com/lchrusciel))
- [#557](https://github.com/Sylius/SyliusShopApiPlugin/issues/557) Made tranlation keys more specific ([@mamazu](https://github.com/mamazu))

### Fixed
- [#501](https://github.com/Sylius/SyliusShopApiPlugin/issues/501) [Login] Fix validation error message ([@lchrusciel](https://github.com/lchrusciel))
- [#522](https://github.com/Sylius/SyliusShopApiPlugin/issues/522) Fix errors in swagger file ([@alexander-schranz](https://github.com/alexander-schranz))
- [#531](https://github.com/Sylius/SyliusShopApiPlugin/issues/531) Fix cors configuration and paths to config file ([@alexander-schranz](https://github.com/alexander-schranz))
- [#536](https://github.com/Sylius/SyliusShopApiPlugin/issues/536) Fix CartBlamerListener without token ([@alexander-schranz](https://github.com/alexander-schranz), [@lchrusciel](https://github.com/lchrusciel))
- [#548](https://github.com/Sylius/SyliusShopApiPlugin/issues/548) Fixing the customer birthday ([@mamazu](https://github.com/mamazu))
- [#530](https://github.com/Sylius/SyliusShopApiPlugin/issues/530) Addding assertions for product in current channel ([@mamazu](https://github.com/mamazu))
- [#554](https://github.com/Sylius/SyliusShopApiPlugin/issues/554) Fixing the type errors ([@mamazu](https://github.com/mamazu))
- [#546](https://github.com/Sylius/SyliusShopApiPlugin/issues/546) Handle registration for exist customer ([@alexander-schranz](https://github.com/alexander-schranz), [@lchrusciel](https://github.com/lchrusciel))
- [#556](https://github.com/Sylius/SyliusShopApiPlugin/issues/556) [Performance] Add back cached channel context ([@lchrusciel](https://github.com/lchrusciel))
- [#555](https://github.com/Sylius/SyliusShopApiPlugin/issues/555) add validation to verify account action and update swagger.yml ([@CSchulz](https://github.com/CSchulz))
- [#558](https://github.com/Sylius/SyliusShopApiPlugin/issues/558) [Images] Fix git ignore in test app ([@lchrusciel](https://github.com/lchrusciel))

## v1.0.0-rc.1

#### TL;DR

- Fixed performance issue for logged in customers ([#478](https://github.com/Sylius/SyliusShopApiPlugin/issues/478))
- Improved customer assignment to the cart, so the promotions and all customer based calculation is done ASAP ([#490](https://github.com/Sylius/SyliusShopApiPlugin/issues/490))
- Easier extending of requests and commands ([#371](https://github.com/Sylius/SyliusShopApiPlugin/issues/371), [#480](https://github.com/Sylius/SyliusShopApiPlugin/issues/480), [#479](https://github.com/Sylius/SyliusShopApiPlugin/issues/479), [#491](https://github.com/Sylius/SyliusShopApiPlugin/issues/491), [#493](https://github.com/Sylius/SyliusShopApiPlugin/issues/493), [#496](https://github.com/Sylius/SyliusShopApiPlugin/issues/496))
- Fixed issues with too many addresses in database ([#349](https://github.com/Sylius/SyliusShopApiPlugin/issues/349))
- Added access to show order for guests ([#443](https://github.com/Sylius/SyliusShopApiPlugin/issues/443))
- Tactician bus was replaced with a Messenger component ([#419](https://github.com/Sylius/SyliusShopApiPlugin/issues/419))
- Integration with JWT was whitelisted and now it is supported out-of-the-box ([#482](https://github.com/Sylius/SyliusShopApiPlugin/issues/482))
- Plugin started to follow Plugin naming convention ([#473](https://github.com/Sylius/SyliusShopApiPlugin/issues/473))
- Address book CRUD operation are hanlded with Resource Controller instead of Command pattern ([#462](https://github.com/Sylius/SyliusShopApiPlugin/issues/462))
- New fields were added to responses like payment instructions to the PaymentMethodView([#348](https://github.com/Sylius/SyliusShopApiPlugin/issues/348)]) or coupon code to the CartView ([#414](https://github.com/Sylius/SyliusShopApiPlugin/issues/414))
- Lots of enhancements, bug fixes and documentation improvements

#### Details

### Added
- [#348](https://github.com/Sylius/SyliusShopApiPlugin/issues/348) Added payment instructions to PaymentMethodView ([@kortwotze](https://github.com/kortwotze))
- [#388](https://github.com/Sylius/SyliusShopApiPlugin/issues/388) Adding validation to the `AddProductReview` endpoint ([@mamazu](https://github.com/mamazu))
- [#390](https://github.com/Sylius/SyliusShopApiPlugin/issues/390) Add missing customer attributes to sylius_shop_api_me ()
- [#349](https://github.com/Sylius/SyliusShopApiPlugin/issues/349) Fixed order addressing filling the database with garbage ([@JakobTolkemit](https://github.com/JakobTolkemit), [@mamazu](https://github.com/mamazu))
- [#414](https://github.com/Sylius/SyliusShopApiPlugin/issues/414) Adding coupon code to cart view ([@mamazu](https://github.com/mamazu))
- [#442](https://github.com/Sylius/SyliusShopApiPlugin/issues/442) adding company and phoneNumber to checkout/address ([@hashnz](https://github.com/hashnz))
- [#262](https://github.com/Sylius/SyliusShopApiPlugin/issues/262) Added ProductAttributeValue serialization for select attribute types ([@gorkalaucirica](https://github.com/gorkalaucirica), [@mamazu](https://github.com/mamazu))
- [#443](https://github.com/Sylius/SyliusShopApiPlugin/issues/443) ShowOrderDetailsAction handles guest customer orders. ([@dlobato](https://github.com/dlobato))
- [#455](https://github.com/Sylius/SyliusShopApiPlugin/issues/455) Adding created date to product review endpoint ([@mamazu](https://github.com/mamazu))

### Changed
- [#356](https://github.com/Sylius/SyliusShopApiPlugin/issues/356) Add better description and docs link in README ([@pjedrzejewski](https://github.com/pjedrzejewski))
- [#362](https://github.com/Sylius/SyliusShopApiPlugin/issues/362) Splitting up the view repositories ([@mamazu](https://github.com/mamazu))
- [#366](https://github.com/Sylius/SyliusShopApiPlugin/issues/366) [Composer] Update ApiTestCase ([@lchrusciel](https://github.com/lchrusciel))
- [#367](https://github.com/Sylius/SyliusShopApiPlugin/issues/367) Splitting up the handlers ([@mamazu](https://github.com/mamazu))
- [#371](https://github.com/Sylius/SyliusShopApiPlugin/issues/371) Make extending commands & requests possible ([@pamil](https://github.com/pamil))
- [#374](https://github.com/Sylius/SyliusShopApiPlugin/issues/374) Unify request command objects ([@Zales0123](https://github.com/Zales0123))
- [#375](https://github.com/Sylius/SyliusShopApiPlugin/issues/375) Abstract creating commands from requests ([@Zales0123](https://github.com/Zales0123))
- [#379](https://github.com/Sylius/SyliusShopApiPlugin/issues/379) Revert "Abstract creating commands from requests" ([@lchrusciel](https://github.com/lchrusciel))
- [#387](https://github.com/Sylius/SyliusShopApiPlugin/issues/387) Moved classes into their own namespace ([@mamazu](https://github.com/mamazu))
- [#385](https://github.com/Sylius/SyliusShopApiPlugin/issues/385) Upgrade to Sylius 1.3 & 1.4 and add support for PHP 7.3 ([@pamil](https://github.com/pamil), [@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994), [@Zales0123](https://github.com/Zales0123))
- [#389](https://github.com/Sylius/SyliusShopApiPlugin/issues/389) Fixed show available shippings and payments endpoints. ([@dlobato](https://github.com/dlobato))
- [#394](https://github.com/Sylius/SyliusShopApiPlugin/issues/394) Fix grammar and typo ([@loevgaard](https://github.com/loevgaard))
- [#396](https://github.com/Sylius/SyliusShopApiPlugin/issues/396) Add official Sylius plugin badge ([@Zales0123](https://github.com/Zales0123))
- [#397](https://github.com/Sylius/SyliusShopApiPlugin/issues/397) Minor fixes to swagger definition. ([@dlobato](https://github.com/dlobato))
- [#395](https://github.com/Sylius/SyliusShopApiPlugin/issues/395) Splitting up the PHPUnit tests ([@mamazu](https://github.com/mamazu))
- [#399](https://github.com/Sylius/SyliusShopApiPlugin/issues/399) Add checkoutCompletedAt field to placedOrders ([@dlobato](https://github.com/dlobato))
- [#404](https://github.com/Sylius/SyliusShopApiPlugin/issues/404) Changing the config key as suggested by pamil #190 ([@mamazu](https://github.com/mamazu))
- [#405](https://github.com/Sylius/SyliusShopApiPlugin/issues/405) Unified route names again ([@mamazu](https://github.com/mamazu))
- [#408](https://github.com/Sylius/SyliusShopApiPlugin/issues/408) [README] Add contributors outside of organization ([@lchrusciel](https://github.com/lchrusciel))
- [#409](https://github.com/Sylius/SyliusShopApiPlugin/issues/409) Minor fixes to the new section in README ([@pamil](https://github.com/pamil))
- [#411](https://github.com/Sylius/SyliusShopApiPlugin/issues/411) [Maintenance] Move commands to proper directories ([@GSadee](https://github.com/GSadee))
- [#412](https://github.com/Sylius/SyliusShopApiPlugin/issues/412) [Maintenance] Move requests to proper directories ([@GSadee](https://github.com/GSadee))
- [#400](https://github.com/Sylius/SyliusShopApiPlugin/issues/400) Add optional auth to checkoutComplete operation ([@dlobato](https://github.com/dlobato))
- [#415](https://github.com/Sylius/SyliusShopApiPlugin/issues/415) Splitting up the views ([@mamazu](https://github.com/mamazu))
- [#417](https://github.com/Sylius/SyliusShopApiPlugin/issues/417) [Maintenance] Update testing paragraph in README file ([@GSadee](https://github.com/GSadee))
- [#419](https://github.com/Sylius/SyliusShopApiPlugin/issues/419) [Maintenance] Switch to Symfony Messenger ([@GSadee](https://github.com/GSadee))
- [#421](https://github.com/Sylius/SyliusShopApiPlugin/issues/421) [Composer] Bump dev dependencies ([@lchrusciel](https://github.com/lchrusciel))
- [#423](https://github.com/Sylius/SyliusShopApiPlugin/issues/423) [Maintenance] Enable some tests ([@GSadee](https://github.com/GSadee))
- [#420](https://github.com/Sylius/SyliusShopApiPlugin/issues/420) Unify product routing ([@mamazu](https://github.com/mamazu))
- [#426](https://github.com/Sylius/SyliusShopApiPlugin/issues/426) [Routing] Unify routings for showing products by taxon code and slug ([@GSadee](https://github.com/GSadee))
- [#430](https://github.com/Sylius/SyliusShopApiPlugin/issues/430) Run analyse only once on Travis CI ([@teohhanhui](https://github.com/teohhanhui))
- [#429](https://github.com/Sylius/SyliusShopApiPlugin/issues/429) Add .editorconfig ([@teohhanhui](https://github.com/teohhanhui))
- [#427](https://github.com/Sylius/SyliusShopApiPlugin/issues/427) [Routing] Remove deprecated product and cart routes ([@GSadee](https://github.com/GSadee))
- [#432](https://github.com/Sylius/SyliusShopApiPlugin/issues/432) Fixed sylius doc url ([@mamazu](https://github.com/mamazu))
- [#437](https://github.com/Sylius/SyliusShopApiPlugin/issues/437) [Maintanance] Remove redundant specs ([@lchrusciel](https://github.com/lchrusciel))
- [#446](https://github.com/Sylius/SyliusShopApiPlugin/issues/446) Require Messenger ~4.3.0 ([@pamil](https://github.com/pamil))
- [#447](https://github.com/Sylius/SyliusShopApiPlugin/issues/447) Add support for Sylius 1.5, remove for Sylius 1.3 ([@pamil](https://github.com/pamil))
- [#450](https://github.com/Sylius/SyliusShopApiPlugin/issues/450) fix product not assigned to channel error message ([@CSchulz](https://github.com/CSchulz))
- [#449](https://github.com/Sylius/SyliusShopApiPlugin/issues/449) fixed product reviews by product code route by api convention ([@antonioperic](https://github.com/antonioperic))
- [#453](https://github.com/Sylius/SyliusShopApiPlugin/issues/453) Adding address mapper ([@mamazu](https://github.com/mamazu))
- [#458](https://github.com/Sylius/SyliusShopApiPlugin/issues/458) Update installation instructions ([@siven-xtn](https://github.com/siven-xtn))
- [#460](https://github.com/Sylius/SyliusShopApiPlugin/issues/460) More LoggedInUserProvider implementation and adding phpstan back in ([@mamazu](https://github.com/mamazu))
- [#463](https://github.com/Sylius/SyliusShopApiPlugin/issues/463) [Checkout] Remove channel code from checkout routes ([@GSadee](https://github.com/GSadee))
- [#465](https://github.com/Sylius/SyliusShopApiPlugin/issues/465) [Order] Remove channel code from order routes ([@GSadee](https://github.com/GSadee))
- [#464](https://github.com/Sylius/SyliusShopApiPlugin/issues/464) [AddressBook] Remove channel code from address book routes ([@GSadee](https://github.com/GSadee))
- [#469](https://github.com/Sylius/SyliusShopApiPlugin/issues/469) [Docs][Order] Update docs after removing channel code from order routes ([@GSadee](https://github.com/GSadee))
- [#470](https://github.com/Sylius/SyliusShopApiPlugin/issues/470) [Product] Remove channel code from product routes ([@GSadee](https://github.com/GSadee))
- [#468](https://github.com/Sylius/SyliusShopApiPlugin/issues/468) [Taxon] Remove channel code from taxon routes ([@GSadee](https://github.com/GSadee))
- [#471](https://github.com/Sylius/SyliusShopApiPlugin/issues/471) [Customer] Remove channel code from customer routes ([@GSadee](https://github.com/GSadee))
- [#472](https://github.com/Sylius/SyliusShopApiPlugin/issues/472) [Cart] Remove channel code from cart routes ([@GSadee](https://github.com/GSadee))
- [#474](https://github.com/Sylius/SyliusShopApiPlugin/issues/474) Update README.md ([@AdamKasp](https://github.com/AdamKasp))
- [#477](https://github.com/Sylius/SyliusShopApiPlugin/issues/477) Update README.md ([@AdamKasp](https://github.com/AdamKasp))
- [#475](https://github.com/Sylius/SyliusShopApiPlugin/issues/475) [POC] Another approach to the command providers ([@lchrusciel](https://github.com/lchrusciel), [@mamazu](https://github.com/mamazu), [@GSadee](https://github.com/GSadee))
- [#483](https://github.com/Sylius/SyliusShopApiPlugin/issues/483) [Maintenance] EOT to JSON rename ([@lchrusciel](https://github.com/lchrusciel))
- [#481](https://github.com/Sylius/SyliusShopApiPlugin/issues/481) [Checkout] Extract cart assignment from order complete ([@lchrusciel](https://github.com/lchrusciel))
- [#486](https://github.com/Sylius/SyliusShopApiPlugin/issues/486) [Maintenance] Minor cs fixes ([@lchrusciel](https://github.com/lchrusciel))
- [#482](https://github.com/Sylius/SyliusShopApiPlugin/issues/482) [Login] Unify and simplify requests ([@lchrusciel](https://github.com/lchrusciel))
- [#473](https://github.com/Sylius/SyliusShopApiPlugin/issues/473) Adjust plugin name to Sylius best practices ([@lchrusciel](https://github.com/lchrusciel), [@mamazu](https://github.com/mamazu))
- [#480](https://github.com/Sylius/SyliusShopApiPlugin/issues/480) [Product] Refactor product actions to use command providers ([@GSadee](https://github.com/GSadee))
- [#487](https://github.com/Sylius/SyliusShopApiPlugin/issues/487) [Customer] Extract customer enabling logic to custom handler ([@lchrusciel](https://github.com/lchrusciel))
- [#484](https://github.com/Sylius/SyliusShopApiPlugin/issues/484) Fixes after review for new command providers concept ([@GSadee](https://github.com/GSadee))
- [#492](https://github.com/Sylius/SyliusShopApiPlugin/issues/492) Decouple plugin from the SyliusShopBundle ([@lchrusciel](https://github.com/lchrusciel), [@Zales0123](https://github.com/Zales0123))
- [#488](https://github.com/Sylius/SyliusShopApiPlugin/issues/488) [Cart] Simplify cart recalculation after customer cart assignment ([@lchrusciel](https://github.com/lchrusciel))
- [#490](https://github.com/Sylius/SyliusShopApiPlugin/issues/490) [Checkout] Use new cart assignment logic in checkout ([@lchrusciel](https://github.com/lchrusciel))
- [#462](https://github.com/Sylius/SyliusShopApiPlugin/issues/462) [RFC] Rework address book from commands to crud ([@Tomanhez](https://github.com/Tomanhez))
- [#479](https://github.com/Sylius/SyliusShopApiPlugin/issues/479) [Cart] Refactor cart actions to use command providers ([@GSadee](https://github.com/GSadee))
- [#459](https://github.com/Sylius/SyliusShopApiPlugin/issues/459) Updating the documentation ([@mamazu](https://github.com/mamazu))
- [#495](https://github.com/Sylius/SyliusShopApiPlugin/issues/495) Remove unused address book view leftovers ([@GSadee](https://github.com/GSadee))
- [#491](https://github.com/Sylius/SyliusShopApiPlugin/issues/491) [Checkout] Refactor checkout actions to use command providers ([@GSadee](https://github.com/GSadee))
- [#493](https://github.com/Sylius/SyliusShopApiPlugin/issues/493) [Customer] Refactor customer actions to use command providers ([@GSadee](https://github.com/GSadee))
- [#497](https://github.com/Sylius/SyliusShopApiPlugin/issues/497) Remove unnecessary prefix in controllers test files ([@GSadee](https://github.com/GSadee))
- [#496](https://github.com/Sylius/SyliusShopApiPlugin/issues/496) [AddressBook] Refactor address book actions to use command providers ([@GSadee](https://github.com/GSadee))
- [#498](https://github.com/Sylius/SyliusShopApiPlugin/issues/498) Change from private to protected for requests constructors ([@GSadee](https://github.com/GSadee))

### Fixed
- [#357](https://github.com/Sylius/SyliusShopApiPlugin/issues/357) ChannelCode taken from url instead of request ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#365](https://github.com/Sylius/SyliusShopApiPlugin/issues/365) Secure checkout complete action ([@mamazu](https://github.com/mamazu), [@lchrusciel](https://github.com/lchrusciel))
- [#376](https://github.com/Sylius/SyliusShopApiPlugin/issues/376) Fixed swagger.yml to match API ([@dlobato](https://github.com/dlobato))
- [#386](https://github.com/Sylius/SyliusShopApiPlugin/issues/386) Fixed shop api /verify-account route ()
- [#398](https://github.com/Sylius/SyliusShopApiPlugin/issues/398) Add security checker as dev dependency ([@dlobato](https://github.com/dlobato))
- [#401](https://github.com/Sylius/SyliusShopApiPlugin/issues/401) Double cart fix ([@mamazu](https://github.com/mamazu))
- [#413](https://github.com/Sylius/SyliusShopApiPlugin/issues/413) Fix handler's service name for address order ([@diimpp](https://github.com/diimpp))
- [#418](https://github.com/Sylius/SyliusShopApiPlugin/issues/418) Fixing the docs ([@mamazu](https://github.com/mamazu))
- [#425](https://github.com/Sylius/SyliusShopApiPlugin/issues/425) Change parameter name to make it consistent with reset password ([@dlobato](https://github.com/dlobato))
- [#439](https://github.com/Sylius/SyliusShopApiPlugin/issues/439) Cart summary now only works for orders in state cart ([@mamazu](https://github.com/mamazu), [@lchrusciel](https://github.com/lchrusciel))
- [#478](https://github.com/Sylius/SyliusShopApiPlugin/issues/478) [Cart] Recalculate customer cart when log in ([@lchrusciel](https://github.com/lchrusciel))
- [#489](https://github.com/Sylius/SyliusShopApiPlugin/issues/489) [HotFix] Try to fix Travis ([@Zales0123](https://github.com/Zales0123))

## v1.0.0-beta.21

#### TL;DR

- Added address book API ([#229](https://github.com/Sylius/ShopApiPlugin/pull/229), [#295](https://github.com/Sylius/ShopApiPlugin/pull/295))
- Added customer profile update API ([#236](https://github.com/Sylius/ShopApiPlugin/pull/236))
- Added Sylius 1.1 and 1.2 support ([#253](https://github.com/Sylius/ShopApiPlugin/pull/253), [#275](https://github.com/Sylius/ShopApiPlugin/pull/275), [#291](https://github.com/Sylius/ShopApiPlugin/pull/291))
- Added Symfony 4.1+ support ([#293](https://github.com/Sylius/ShopApiPlugin/pull/293), [#294](https://github.com/Sylius/ShopApiPlugin/pull/294))
- Added stable Tactician command bus support, removed support for prestable one ([#293](https://github.com/Sylius/ShopApiPlugin/pull/293))
- Removed Sylius 1.0 support ([#276](https://github.com/Sylius/ShopApiPlugin/pull/276))
- Lots of enhancements, bug fixes and documentation improvements

#### Details

- [#224](https://github.com/Sylius/ShopApiPlugin/pull/224) [README] Remove warning (@lchrusciel)
- [#225](https://github.com/Sylius/ShopApiPlugin/pull/225) add composer suggestions (@sdleiw)
- [#227](https://github.com/Sylius/ShopApiPlugin/pull/227) fix nelmio_cors bundle config in the readme, (@sdleiw)
- [#229](https://github.com/Sylius/ShopApiPlugin/pull/229) [RFC] Address book (@jseparovic1)
- [#236](https://github.com/Sylius/ShopApiPlugin/pull/236) [RFC] Customer profile update api (@zenjara)
- [#237](https://github.com/Sylius/ShopApiPlugin/pull/237) Add latest products (@sdleiw)
- [#239](https://github.com/Sylius/ShopApiPlugin/pull/239) Get quantity from request as an integer. (@lchrusciel)
- [#240](https://github.com/Sylius/ShopApiPlugin/pull/240) update nelmio_cors config, (@sdleiw)
- [#245](https://github.com/Sylius/ShopApiPlugin/pull/245) add description and shortdescription on view product (@chefdeprojetrc)
- [#246](https://github.com/Sylius/ShopApiPlugin/pull/246) Fix an issue in the configuration example which causes error in loading checkout page. (@Hailong)
- [#248](https://github.com/Sylius/ShopApiPlugin/pull/248) Correct the error message for ChooseShippingMethod API. (@Hailong)
- [#253](https://github.com/Sylius/ShopApiPlugin/pull/253) Use Sylius v1.1.0-RC (@Zales0123)
- [#254](https://github.com/Sylius/ShopApiPlugin/pull/254) More builds! (@pamil)
- [#270](https://github.com/Sylius/ShopApiPlugin/pull/270) [README] Mention about shop api mailing config (@lchrusciel)
- [#274](https://github.com/Sylius/ShopApiPlugin/pull/274) Fixed ShowAvailableShippingMethodsAction constructor argument typehinting to use interfaces (@cyrosy)
- [#275](https://github.com/Sylius/ShopApiPlugin/pull/275) [Maintenance] Allow Sylius 1.2 (@lchrusciel)
- [#276](https://github.com/Sylius/ShopApiPlugin/pull/276) Drop support for Sylius 1.0 (@pamil)
- [#280](https://github.com/Sylius/ShopApiPlugin/pull/280) [Maintenance] Remove outdated services (@lchrusciel)
- [#286](https://github.com/Sylius/ShopApiPlugin/pull/286) Removed unused cart controller (@mamazu)
- [#288](https://github.com/Sylius/ShopApiPlugin/pull/288) Added more documentation to swagger (@mamazu)
- [#289](https://github.com/Sylius/ShopApiPlugin/pull/289) Fix tests (order products by their id) (@pamil)
- [#291](https://github.com/Sylius/ShopApiPlugin/pull/291) Tweak Travis configuration to test the plugin with Sylius 1.2 and 1.3 (@pamil)
- [#292](https://github.com/Sylius/ShopApiPlugin/pull/292) Use SyliusLabs/CodingStandard v2 (@pamil)
- [#293](https://github.com/Sylius/ShopApiPlugin/pull/293) Update various dependencies (@pamil)
- [#294](https://github.com/Sylius/ShopApiPlugin/pull/294) Test on Travis CI using different Symfony versions (@pamil)
- [#295](https://github.com/Sylius/ShopApiPlugin/pull/295) Added documentation for the address book (@mamazu)
- [#299](https://github.com/Sylius/ShopApiPlugin/pull/299) [README] Pre release improvements (@lchrusciel)
- [#301](https://github.com/Sylius/ShopApiPlugin/pull/301) More precise error messages (@mamazu)
- [#302](https://github.com/Sylius/ShopApiPlugin/pull/302) Unified routing (@mamazu)
- [#304](https://github.com/Sylius/ShopApiPlugin/pull/304) Replaced asserts with type hints (@mamazu)
- [#305](https://github.com/Sylius/ShopApiPlugin/pull/305) Improve coupon code validator (@lchrusciel)
- [#306](https://github.com/Sylius/ShopApiPlugin/pull/306) Random minor fixes (@lchrusciel)
- [#307](https://github.com/Sylius/ShopApiPlugin/pull/307) Codebase cleanup (@lchrusciel)
- [#308](https://github.com/Sylius/ShopApiPlugin/pull/308) [Maintenance] Add phpstan  (@lchrusciel)
