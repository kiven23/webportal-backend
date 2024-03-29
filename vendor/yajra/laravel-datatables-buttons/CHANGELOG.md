# Laravel DataTables Buttons Plugin CHANGELOG.

## v3.3.1 - 01-23-2019

- Allow Laravel 5.4 and up [#87, credits to @barryvdh.

## v3.3.0 - 08-15-2018

- Implemented the 'exclude column from export' [#65], credits to @rylxes.

## v3.2.2 - 07-31-2018

- Keep original type in exportable array. [#67] credits to @FabienLucini.

## v3.2.1 - 02-21-2018

- Add reset to column search and icons to copy and colvis buttons [#59], credits to @Namoshek.

## v3.2.0 - 02-11-2018

- Add support for Laravel 5.6. [#57]

## v3.1.2 - 12-23-2017

- Change the default export filename [#49], credits to @ElfSundae.

## v3.1.1 - 11-23-2017

- Specify selected columns to avoid issues like [#19] and [yajra/laravel-datatables#1519](https://github.com/yajra/laravel-datatables/issues/1519). [#54]

## v3.1.0 - 10-27-2017

- Add export CSV with post as a method. [#41], credits to @jiwom
- Add export PDF with post as a method. [#42], credits to @jiwom
- Add export Excel with post as a method. [#43], credits to @jiwom

## v3.0.2 - 10-18-2017

- Fix ButtonsServiceProvider. [#39], credits to @ElfSundae
- Fix doc PR links.

## v3.0.1 - 10-05-2017

- Handle scopes stub folder config. [#37]
- Use default builder parameters from config. [#36]
- Use datatables() helper to create dataTable instance. [#35]
- Credits to @ElfSundae.

## v3.0.0 - 08-31-2017

- Drop support for `laravel-datatables-oracle:7.*`.
- Exclusive support for `laravel-datatables-oracle:8.*`.
- Change namespace from `Yajra\Datatables` to `Yajra\DataTables`.
- Use `snappy` as default pdf generator.
- Remove constructor dependencies. Create DataTable instance directly from `dataTable()` method.
- Method injections are now supported on the following methods:

```php
ajax(), dataTable(), query(), csv(), excel(), pdf(), printPreview()
```

- `DataTableContract` contract removed.
- `DataTableScopeContract` contract renamed to `DataTableScope`.
- `DataTableButtonsContract` contract renamed to `DataTableButtons`.

## v2.0.2 - 06-30-2017

- Fix min php requirements.
- Import data transformer that was removed from main package (v8.x).

## v2.0.1 - 06-28-2017

- Allow tests failure on PHP 5.6.

## v2.0.0 - 06-28-2017

- Add support for Laravel 5.5.
- Upgrade to laravel-datatables-html:~2.0

## v1.3.2 - 06-26-2017

- Use minifiedAjax by default. [#24]
- Update html package to min v1.4
- Fix random issues occurring due to long URL.
- Fix/Lessen IE compatibility issue due to long URL limitation.

## v1.3.1 - 05-28-2017

- Inline builder parameters and include `dom` on stub to assist new users. #20

## v1.3.0 - 04-20-2017

- Adding model-namespace, columns and action options to Generator [#12], credits to @lk77.
- Allow to add custom button actions (or disable already existing ones). #18, credits to @underdpt.

## v1.2.1 - 03-28-2017

- Do not require return from before callback closure. [#14]

## v1.2.0 - 03-28-2017

- Add html builder callback for code re-usability. [#13]

## v1.1.2 - 03-08-2017

- Fix url params js script.
- Fix https://github.com/yajra/laravel-datatables/issues/1049.

## v1.1.1 - 02-17-2017

- Remove config that is set on main repo.
- Docs, use ^1.1 when installing.

## v1.1.0 - 02-16-2017

- Add before and response callback.
- Fix and update datatables generator.
- Use phpunit 5.7 to match Laravel’s requirement.
- Add missing require illuminate/console.
- Refactor dataTable ajax response for reusability.
- Remove methods that are available on abstract class.

## v1.0.1 - 02-16-2017

- Fix ajax parameters when ? was used on based url.
- PR [#5], credits to @OzanKurt.

## v1.0.0 - 01-27-2017

- First stable release.

[#87]: https://github.com/yajra/laravel-datatables-buttons/pull/87
[#65]: https://github.com/yajra/laravel-datatables-buttons/pull/65
[#67]: https://github.com/yajra/laravel-datatables-buttons/pull/67
[#59]: https://github.com/yajra/laravel-datatables-buttons/pull/59
[#57]: https://github.com/yajra/laravel-datatables-buttons/pull/57
[#49]: https://github.com/yajra/laravel-datatables-buttons/pull/49
[#54]: https://github.com/yajra/laravel-datatables-buttons/pull/54
[#43]: https://github.com/yajra/laravel-datatables-buttons/pull/43
[#42]: https://github.com/yajra/laravel-datatables-buttons/pull/42
[#41]: https://github.com/yajra/laravel-datatables-buttons/pull/41
[#39]: https://github.com/yajra/laravel-datatables-buttons/pull/39
[#37]: https://github.com/yajra/laravel-datatables-buttons/pull/37
[#36]: https://github.com/yajra/laravel-datatables-buttons/pull/36
[#35]: https://github.com/yajra/laravel-datatables-buttons/pull/35
[#24]: https://github.com/yajra/laravel-datatables-buttons/pull/24
[#14]: https://github.com/yajra/laravel-datatables-buttons/pull/14
[#13]: https://github.com/yajra/laravel-datatables-buttons/pull/13
[#12]: https://github.com/yajra/laravel-datatables-buttons/pull/12
[#5]: https://github.com/yajra/laravel-datatables-buttons/pull/5

[#19]: https://github.com/yajra/laravel-datatables-buttons/issues/19
