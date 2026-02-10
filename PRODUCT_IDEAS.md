# Produktové nápady a otázky pro Gastro projekt

Na základě vašeho zadání a analýzy kódu jsem připravil následující postřehy a nápady, které by mohly pomoci v dalším rozvoji aplikace.

## 1. Bezpečnost 4-místných kódů
Zkrácení kódů na 4 znaky (čísla + písmena) výrazně zrychlí odbavení, ale přináší rizika:
- **Pravděpodobnost uhodnutí:** Při použití znakové sady A-Z, 0-9 (36 znaků) existuje cca 1,6 milionu kombinací. Pokud budete mít v oběhu tisíce platných voucherů, útočník by mohl náhodným zkoušením trefit platný kód.
- **Doporučení:**
  - Zavést **Rate Limiting** na skeneru (např. po 5 chybných pokusech zablokovat možnost zadávání na 5 minut).
  - Kódy generovat tak, aby se neopakovaly v krátkém časovém úseku.
  - V administraci sledovat "Failed Scan Attempts".

## 2. CRM a Práce se zákazníky
Požadavek na "historii zákazníka" je skvělým krokem k CRM (Customer Relationship Management).
- **Slučování identit:** Zákazníci mohou pokaždé zadat jiný email nebo překlep. Bylo by vhodné časem zavést párování podle telefonního čísla (unikátnější identifikátor).
- **Segmentace:** V "Detailu zákazníka" by se mohlo ukazovat:
  - "VIP" status (např. po 5 návštěvách).
  - Průměrná útrata (pokud by se evidovala hodnota voucherů).
  - Poslední návštěva (pro reaktivační kampaně).
- **Automatizace:** Po uplatnění voucheru odeslat zákazníkovi (pokud dal souhlas) email: "Jak vám chutnalo?" s odkazem na Google Recenze.

## 3. Workflow Personálu
- **Rychlé potvrzení:** Personál by měl mít možnost vidět nejen "O jakou akci jde", ale i případné poznámky k zákazníkovi (např. "Alergie na ořechy" z minula, pokud by se evidovalo).
- **Upsell (Navyšování prodeje):** Při skenování voucheru na kávu by systém mohl personálu zobrazit "Doporuč nabídnout zákusek dne".
- **Kitchen Display:** Pokud je voucher na jídlo, po jeho uplatnění by se mohl automaticky vytisknout bon v kuchyni (pokud by bylo napojeno na tiskárnu).

## 4. GDPR a Data
- Přidání souhlasu (checkbox) je nutnost. Doporučuji ukládat nejen `true/false`, ale i **datum a čas udělení souhlasu** a **text souhlasu** (verzi), pro případnou kontrolu.
- **Expirace dat:** Data o neaktivních zákaznících by se měla po určité době (např. 3 roky) anonymizovat.

## 5. Gamifikace pro zákazníky
- **Věrnostní program:** Za každý 5. uplatněný voucher automaticky generovat voucher zdarma. Systém to již v podstatě umožňuje sledováním historie.
- **Soutěže:** "Najdi ukrytý kód na webu/v menu" -> Zadá do formuláře -> Získá slevu.

## 6. Technické vylepšení
- **Offline režim:** Co když vypadne internet? Skener by mohl (teoreticky) fungovat offline, pokud by se seznam platných hashů stáhl do zařízení na začátku směny (náročnější na implementaci, ale bezpečnější pro provoz).
- **PWA:** Webovou aplikaci lze upravit tak, aby šla "nainstalovat" na plochu tabletu/mobilu personálu, což zlepší přístup k fotoaparátu a UX.
