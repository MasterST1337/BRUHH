# Isakram · Fødevarecentralen

En app, der forbinder lokale mikroproducenter med kunder i nærområdet og bekæmper madspild. Producenterne lægger varer med kort dato ud på et kort. Kunderne reserverer og henter. Det, ingen når at hente, laver Isakram-køkkenet på Aagade 30 til mad.

Isakram tager 10 % af salgsprisen. Producenten får 90 %, og der er intet oprettelsesgebyr og intet abonnement.

## Hvad appen kan

**Kunde**
- Kort over producenter med varer og tilbud. Nålene har kategori-ikoner, og ringens farve viser, hvor kort datoen er.
- Filtrér på afstand, kategori og "kun sidste dag".
- Betal med MobilePay eller kort. Beløbet reserveres ved bestilling og trækkes først ved afhentning (simuleret).
- Få en afhentningskode, og annullér selv, hvis planerne ændrer sig.

**Bliv producent**
- Tilmelding i fem trin: CVR-opslag, bekræftelse af telefon, kategorier (gerne flere), afhentningssted og producentaftale.
- Status efter tilmelding med trin-bjælke: Modtaget → Godkendelse → Shop åbner.

**Din shop** (når Isakram har godkendt producenten)
- Overblik over salg, ordrer til afhentning og hvad producenten laver.
- Produkter: tag et billede med bagkameraet, og Claude genkender varen og udfylder navn, enhed, hylde og beskrivelse.
- Tilbud: læg varer med kort dato ud. Prisen falder automatisk mod datoen.
- Udbetaling: opret betalingskonto, se salg, Isakrams 10 % og ugentlige udbetalinger med afregningsbilag (simuleret).

**Isakram**
- Godkend producenter og varer, se alle varer i omløb og køkkenets uge med forslag fra kogebøger.
- Økonomi: pengestrømmen fra kunde til producent, provision, betalingsgebyrer og udbetalinger.

## Filer

| Mappe | Indhold |
|---|---|
| `web/` | Versionen til hjemmesiden. Kører uden login, og intet gemmes. |
| `web/fodevarecentralen.html` | Selve appen. Én selvstændig fil. |
| `web/fodevarecentralen-sektion.js` | Tilføjer "Fødevarecentralen" som en side i menuen på hjemmesiden, med appen vist i en telefonramme. |
| `web/sprog.js` | Sprogvalg (DA · EN · DE) i topbaren. Oversætter hjemmesiden og sender sproget videre til appen. |
| `web/genkend.php` | Billedgenkendelse. Sender billedet til Claude med en API-nøgle, der aldrig når browseren. |
| `web/genkend-config.example.php` | Skabelon til indstillinger og API-nøgle. |
| `claude/fodevarecentralen-claude.html` | Versionen, der kører som artefakt i Claude, med fælles database og Claude-funktioner. |
| `claude/fodevarecentralen-demo.html` | Demoversion til præsentation. Intet gemmes. |

## Sæt den op på en hjemmeside

1. Læg filerne fra `web/` i samme mappe som hjemmesidens `index.html`.
2. Tilføj denne linje i `index.html` lige før `</body>`:
   ```html
   <script src="fodevarecentralen-sektion.js?v=10"></script>
   <script src="sprog.js?v=1"></script>
   ```
   Hæv tallet, hver gang appen opdateres, så browsere henter den nye version. Tallet i `APP_URL` øverst i `fodevarecentralen-sektion.js` skal også hæves.
3. Billedgenkendelse (valgfri, kræver PHP med curl):
   - Kopiér `genkend-config.example.php` til `genkend-config.php`. Læg den helst én mappe over `public_html`.
   - Indsæt en API-nøgle fra [Claude-konsollen](https://platform.claude.com/settings/keys). Lav den til ét workspace og med udløb "Never".
   - Ret `GENKEND_DOMAENER` til jeres domæne.
   - Uden config-filen virker appen stadig. Producenten udfylder så felterne selv.

## Sprog

Hjemmesiden og appen findes på dansk, engelsk og tysk. Dansk er standard. Valget huskes i browseren, og `?lang=en` eller `?lang=de` i adressen vælger sproget direkte. Oversættelserne ligger som ordbøger i `sprog.js` (hjemmesiden) og øverst i appen. Producenternes egne tekster og opskrifterne fra Madam Mangors kogebog vises på dansk.

## Kort

Hjemmesideversionen bruger CARTO Basemaps. Nøglen står i `CARTO_KEY` i `web/fodevarecentralen.html`. Kortnøgler er synlige i browseren, så begræns den til jeres domæne i CARTO-dashboardet.

Kortdata: © OpenStreetMap-bidragydere, © CARTO. Kortbibliotek: [Leaflet](https://leafletjs.com).

## Status

Proof of concept. CVR-opslag, SMS-bekræftelse og betaling er simuleret med demodata. Betalingen er tænkt bygget på Stripe Connect med manuel trækning: Isakrams 10 % som platformsgebyr og resten direkte til producentens egen konto. En rigtig udgave skal slå CVR-numre op via en server-API og sende SMS-koder gennem en SMS-tjeneste.
