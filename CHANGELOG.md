# CHANGELOG FOR `1.0.X`

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
