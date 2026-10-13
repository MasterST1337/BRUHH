# Betalingsmodeller i Fødevarecentralen

Appen har to afregningsmodeller. Producenten vælger model ved tilmelding og kan skifte under Din shop → Udbetaling. Hver reservation gemmer den model og andel, den blev solgt under, så et skift kun gælder nye salg.

Fælles for begge: kunden betaler i appen med MobilePay eller kort. Beløbet reserveres ved bestilling og trækkes først ved afhentning. Annullerer kunden, eller bliver varen ikke hentet, frigives beløbet, og varen kommer tilbage på hylden.

## Model 1: Direkte betaling (90/10)

- Producenten sælger, og Isakram formidler.
- Betalingen deles automatisk: 90 % til producentens egen Stripe-konto og 10 % til Isakram som platformsgebyr.
- Udbetaling hver fredag.
- Producenten opretter en Stripe-konto (CVR, bankkonto, legitimation).
- Isakram rører aldrig producentens penge, så Isakram skal ikke have tilladelse som betalingstjeneste.
- Teknisk: Stripe Connect med manuel trækning (capture) og platformsgebyr.

## Model 2: Kontoafregning (50/50)

- Isakram sælger varen videre i eget navn og står for kunden, afhentning og eventuel levering.
- Kunden betaler Isakram. Producentens andel på 50 % samles på en konto hos Isakram.
- Overførsel til producentens bankkonto den 1. i hver måned, med en kontoafregning som bilag.
- Producenten skal kun oplyse en bankkonto.
- Teknisk: en almindelig Stripe-konto for Isakram, ingen Connect.

## Eksempel på et salg til 100 kr.

| | Direkte betaling | Kontoafregning |
|---|---|---|
| Producenten får | 90 kr. | 50 kr. |
| Isakram får | 10 kr. | 50 kr. |
| Betalingsgebyr (eksempel: 1,5 % + 1,80 kr.) | −3,30 kr. | −3,30 kr. |
| Isakram netto | 6,70 kr. | 46,70 kr. |

Gebyrsatsen er et eksempel og ikke Stripes faktiske pris.

## Skal afklares

- Moms: Som formidler (model 1) opkræver Isakram typisk kun moms af provisionen. Som videresælger (model 2) er hele salget Isakrams omsætning. Tal med en revisor.
- Hvem bærer tabet, når en kunde ikke henter varen?
- Kurve med varer fra flere producenter: én betaling pr. producent, eller én samlet betaling, som Isakram deler bagefter.
