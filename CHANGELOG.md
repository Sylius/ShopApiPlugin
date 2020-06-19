# CHANGELOG FOR `1.1.X`

## v1.1.0

#### TL;DR

- Added more validtio
- Improvements in different locale support
- Login protection for addressbook routes

#### Details

### Added
- [#600](https://github.com/Sylius/ShopApiPlugin/issues/600) [ENHANCEMENT] Added province code to AddressView
- [#592](https://github.com/Sylius/ShopApiPlugin/issues/592) Adding accept language to locale listener ([@mamazu](https://github.com/mamazu))
- [#525](https://github.com/Sylius/ShopApiPlugin/issues/525) Add route for changing password of current user ([@nnatter](https://github.com/nnatter))
- [#616](https://github.com/Sylius/ShopApiPlugin/issues/616) Protect Address Book and me routes ([@Amr3zzat](https://github.com/Amr3zzat), [@mamazu](https://github.com/mamazu))
- [#626](https://github.com/Sylius/ShopApiPlugin/issues/626) More comprehensive error messages for checkout validation ([@mamazu](https://github.com/mamazu))
- [#549](https://github.com/Sylius/ShopApiPlugin/issues/549) Add phoneNumber ans subscribedToNewsletter to /register ([@adrianmarte](https://github.com/adrianmarte))
- [#508](https://github.com/Sylius/ShopApiPlugin/issues/508) Add availibility property to ProductVariantView ([@alexander-schranz](https://github.com/alexander-schranz), [@mamazu](https://github.com/mamazu))
- [#422](https://github.com/Sylius/ShopApiPlugin/issues/422) Implemented order available payment methods and order update payment ([@dlobato](https://github.com/dlobato), [@mamazu](https://github.com/mamazu))
- [#633](https://github.com/Sylius/ShopApiPlugin/issues/633) Add documentation details on how to extends views ([@remy-theroux](https://github.com/remy-theroux))
- [#636](https://github.com/Sylius/ShopApiPlugin/issues/636) Adding a documentation validator to the project ([@mamazu](https://github.com/mamazu))
- [#644](https://github.com/Sylius/ShopApiPlugin/issues/644) Allow customizing picked up cart's locale ([@dnna](https://github.com/dnna))

### Changed
- [#606](https://github.com/Sylius/ShopApiPlugin/issues/606) Adding a test for skipped shipping and payment. ([@mamazu](https://github.com/mamazu))
- [#617](https://github.com/Sylius/ShopApiPlugin/issues/617) Add Validation on Customer Gender ([@Amr3zzat](https://github.com/Amr3zzat))
- [#622](https://github.com/Sylius/ShopApiPlugin/issues/622) Upgrade to PHPStan 0.12 ([@GSadee](https://github.com/GSadee))
- [#611](https://github.com/Sylius/ShopApiPlugin/issues/611) [Doc] fix `checkoutState` parameter in `PlacedOrder` swagger definition (, [@EmilMassey](https://github.com/EmilMassey))
- [#624](https://github.com/Sylius/ShopApiPlugin/issues/624) Removing dead validation for nonexisting class ([@mamazu](https://github.com/mamazu))
- [#623](https://github.com/Sylius/ShopApiPlugin/issues/623) Fixing php type errors instead of ignoring them ([@mamazu](https://github.com/mamazu))
- [#630](https://github.com/Sylius/ShopApiPlugin/issues/630) docs: in PlacedOrder definition add number property
- [#631](https://github.com/Sylius/ShopApiPlugin/issues/631) Define release cycle for Shop API ([@pamil](https://github.com/pamil))
- [#632](https://github.com/Sylius/ShopApiPlugin/issues/632) Update README.md ([@luciano-jr](https://github.com/luciano-jr))
- [#642](https://github.com/Sylius/ShopApiPlugin/issues/642) Make the note about ShopBundle conflict more visible ([@Roshyo](https://github.com/Roshyo))
- [#646](https://github.com/Sylius/ShopApiPlugin/issues/646) Fix typos ([@pgrimaud](https://github.com/pgrimaud))

### Fixed
- [#620](https://github.com/Sylius/ShopApiPlugin/issues/620) Fixing the ecs dependency problems ([@mamazu](https://github.com/mamazu))
- [#612](https://github.com/Sylius/ShopApiPlugin/issues/612) refactor: pass checkout state as criterion to findBy method ([@mamazu](https://github.com/mamazu))
- [#621](https://github.com/Sylius/ShopApiPlugin/issues/621) Add email validation to RequestResetPassword ([@Amr3zzat](https://github.com/Amr3zzat), [@mamazu](https://github.com/mamazu))
- [#643](https://github.com/Sylius/ShopApiPlugin/issues/643) Fixing the documentation parser ([@mamazu](https://github.com/mamazu))

