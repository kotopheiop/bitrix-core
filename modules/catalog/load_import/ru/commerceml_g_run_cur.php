<?

$base_currency = (CCurrency::GetByID('RUR') ? 'RUR' : 'RUB');

$arCMLCurrencies = array(
    "USD" => "USD",
    "EUR" => "EUR",
    "RUR" => $base_currency,
    "RUB" => $base_currency,
    "���." => $base_currency,
    "���" => $base_currency
);
?>