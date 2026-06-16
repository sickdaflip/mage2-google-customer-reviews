# FlipDev_GoogleCustomerReviews

Injects the **Google Customer Reviews survey opt-in** script on the Magento 2 order confirmation page (`checkout_onepage_success`).

- No badge — opt-in only, as required by Google Merchant Center
- Resolves order data (ID, email, delivery country, estimated delivery date) from the checkout session
- Calculates estimated delivery date from order placement + configurable business days (skips weekends)
- Derives GCR language automatically from store locale, or set manually per store
- Fully store-scope-aware Admin System Config
- i18n: `en_US`, `en_GB`, `de_DE`, `es_ES`, `fr_FR`

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.4 |
| Magento | ^2.4.8 |

## Installation

```bash
composer config repositories.flipdev-gcr vcs https://github.com/sickdaflip/mage2-google-customer-reviews
composer require sickdaflip/mage2-google-customer-reviews
bin/magento module:enable FlipDev_GoogleCustomerReviews
bin/magento setup:upgrade
bin/magento cache:flush
```

## Configuration

**Stores → Configuration → FlipDev → Google Customer Reviews**

| Field | Description |
|---|---|
| Enable Survey Opt-in | Activates the opt-in script on the success page |
| Merchant Center ID | Your numeric Google Merchant Center ID |
| Estimated Delivery Days | Business days after order placement (weekends skipped). Default: `5` |
| Opt-in Dialog Style | Position of the survey dialog (`CENTER_DIALOG`, `BOTTOM_RIGHT_DIALOG`, etc.) |
| Survey Language | GCR language code, or `Auto` to derive from store locale |

## How It Works

1. Customer places order → lands on `checkout/onepage/success`
2. Block reads order data from `Magento\Checkout\Model\Session::getLastRealOrder()`
3. Template renders the GCR `platform.js` + `surveyoptin.render()` call before `</body>`
4. Google sends the customer a survey email after the estimated delivery date

## Uninstall

```bash
bin/magento module:uninstall FlipDev_GoogleCustomerReviews
bin/magento setup:upgrade
bin/magento cache:flush
```

## License

MIT
