---
layout: default
title: vtc - Vtiger Command Line Client
---

# vtc

**vtc** is a command-line tool for interacting with Vtiger CRM instances via Webservices.
It lets you query, create, update, and manage CRM data directly from your terminal.

---

## Installation

### Via Composer (global)

```bash
composer global require javanile/vtiger-client
```

### Via download diretto

Scarica il file `vtc.phar` dalla [release page](https://github.com/javanile/vtiger-client/releases) e rendilo eseguibile:

```bash
curl -L -o /usr/local/bin/vtc https://github.com/javanile/vtiger-client/releases/latest/download/vtc.phar
chmod +x /usr/local/bin/vtc
```

### Build dal sorgente

```bash
git clone https://github.com/javanile/vtiger-client.git
cd vtiger-client
make install
```

---

## Quick Start

### 1. Inizializza la configurazione

```bash
vtc init
```

Questo crea il file `vtiger.config.json` nella directory corrente con i valori di default:

```json
{
    "vtiger_url": "https://demo.vtiger.com/vtigercrm/",
    "username": "admin",
    "access_key": "PcC1w0COZDYbqBi"
}
```

Modifica i valori con i dati del tuo CRM:

```bash
nano $(vtc config:file)
```

### 2. Testa la connessione

```bash
vtc ping
```

Se la risposta contiene `"success": true`, la connessione funziona.

### 3. Esegui una query

```bash
vtc query "SELECT * FROM Contacts LIMIT 5"
```

---

## La variabile CRM

La variabile d'ambiente `CRM` e' la funzionalita' chiave di vtc. Permette di gestire **piu' istanze Vtiger CRM** dallo stesso terminale, senza cambiare directory o file di configurazione.

### Come funziona

Quando imposti `CRM`, vtc cerca la configurazione in `~/.vtc/<nome-crm>/vtiger.config.json` anziche' nella directory corrente.

```
CRM non impostata  -->  ./vtiger.config.json
CRM=produzione     -->  ~/.vtc/produzione/vtiger.config.json
CRM=staging        -->  ~/.vtc/staging/vtiger.config.json
CRM=demo           -->  ~/.vtc/demo/vtiger.config.json
```

### Configurare piu' CRM

**Passo 1** - Inizializza ogni istanza con la variabile `CRM`:

```bash
CRM=produzione vtc init
CRM=staging vtc init
CRM=demo vtc init
```

**Passo 2** - Modifica la configurazione di ciascuna:

```bash
nano $(CRM=produzione vtc config:file)
nano $(CRM=staging vtc config:file)
nano $(CRM=demo vtc config:file)
```

**Passo 3** - Testa le connessioni:

```bash
CRM=produzione vtc ping
CRM=staging vtc ping
CRM=demo vtc ping
```

### Usare CRM nei comandi quotidiani

Ogni comando vtc rispetta la variabile `CRM`:

```bash
# Query sul CRM di produzione
CRM=produzione vtc query "SELECT * FROM Contacts LIMIT 10"

# Descrivi un modulo sullo staging
CRM=staging vtc describe Accounts

# Recupera un record dal CRM demo
CRM=demo vtc retrieve 12x1020
```

### Esportare CRM per la sessione

Se lavori a lungo su un'istanza specifica, esporta la variabile per l'intera sessione del terminale:

```bash
export CRM=produzione
vtc ping                    # usa "produzione"
vtc query "SELECT * FROM Leads"  # usa "produzione"
vtc describe Contacts       # usa "produzione"
```

Per tornare alla configurazione locale:

```bash
unset CRM
```

### Alias per accesso rapido

Aggiungi alias al tuo `.bashrc` o `.zshrc` per velocizzare il lavoro:

```bash
alias vtc-prod='CRM=produzione vtc'
alias vtc-stag='CRM=staging vtc'
alias vtc-demo='CRM=demo vtc'
```

Poi usali direttamente:

```bash
vtc-prod query "SELECT * FROM Accounts"
vtc-stag describe Leads
vtc-demo ping
```

---

## Comandi

### `vtc init`

Crea il file di configurazione `vtiger.config.json`.

```bash
vtc init              # nella directory corrente
CRM=miocrm vtc init  # in ~/.vtc/miocrm/
```

### `vtc ping`

Testa la connessione al CRM eseguendo login e challenge.

```bash
vtc ping
CRM=produzione vtc ping
```

### `vtc config:dir`

Mostra la directory dove si trova il file di configurazione.

```bash
vtc config:dir
# Output: /home/user/progetto

CRM=produzione vtc config:dir
# Output: /home/user/.vtc/produzione
```

### `vtc config:file`

Mostra il path completo del file di configurazione. Utile in combinazione con editor:

```bash
nano $(vtc config:file)
nano $(CRM=produzione vtc config:file)
```

### `vtc query "QUERY"`

Esegue una query Webservice. La sintassi e' simile a SQL con alcune limitazioni di Vtiger.

```bash
# Tutti i contatti
vtc query "SELECT * FROM Contacts"

# Contatti filtrati
vtc query "SELECT firstname, lastname FROM Contacts WHERE lastname = 'Rossi'"

# Con LIMIT
vtc query "SELECT * FROM Leads LIMIT 20"

# Account con campo specifico
vtc query "SELECT accountname, phone FROM Accounts WHERE industry = 'Technology'"
```

### `vtc listtypes`

Elenca tutti i moduli (tipi) disponibili nel CRM.

```bash
vtc listtypes
CRM=produzione vtc listtypes
```

### `vtc describe MODULE [DEPTH]`

Descrive la struttura di un modulo: campi, tipi, obbligatorieta', valori ammessi.

```bash
# Struttura base
vtc describe Contacts

# Con profondita' (risolve i riferimenti)
vtc describe Contacts 1
vtc describe Contacts 2
```

### `vtc retrieve CRMID [DEPTH]`

Recupera un singolo record tramite il suo CRMID (formato `NUMEROxNUMERO`).

```bash
vtc retrieve 12x1020
vtc retrieve 12x1020 1    # con profondita'
```

### `vtc create MODULE ELEMENT`

Crea un nuovo record. L'elemento puo' essere passato come JSON inline o da file con `@`.

```bash
# JSON inline
vtc create Contacts '{"firstname": "Mario", "lastname": "Rossi", "email": "mario@example.com"}'

# Da file JSON
vtc create Contacts @contatto.json
```

### `vtc update MODULE ELEMENT`

Aggiorna un record esistente. Il campo `id` deve essere presente nell'elemento.

```bash
# JSON inline (con id)
vtc update Contacts '{"id": "12x1020", "phone": "+39 02 1234567"}'

# Da file
vtc update Contacts @aggiornamento.json

# Con id separato
vtc update Contacts 12x1020 @dati.json
```

### `vtc revise MODULE ELEMENT`

Aggiorna parzialmente un record (solo i campi specificati).

```bash
# Da file
vtc revise Contacts @revisione.json

# Con id e campo singolo
vtc revise Contacts 12x1020 phone "+39 02 9999999"

# Con id e file
vtc revise Contacts 12x1020 @dati-parziali.json
```

---

## Scenari d'uso

### Monitoraggio multi-CRM

Controlla lo stato di tutte le istanze con un semplice script:

```bash
#!/bin/bash
for crm in produzione staging demo; do
    echo "--- $crm ---"
    CRM=$crm vtc ping
    echo
done
```

### Export dati

Esporta dati in formato JSON per analisi:

```bash
CRM=produzione vtc query "SELECT * FROM Contacts" > contatti.json
```

### Automazione con cron

Sincronizza o esporta dati periodicamente:

```bash
# crontab -e
0 * * * * CRM=produzione /usr/local/bin/vtc query "SELECT * FROM Leads WHERE createdtime > '2024-01-01'" >> /var/log/leads.json
```

### Pipeline CI/CD

Usa vtc per verificare dati o configurazioni in pipeline automatiche:

```bash
export CRM=test
vtc ping || exit 1
vtc query "SELECT count(*) FROM Accounts"
```

---

## File di configurazione

Il file `vtiger.config.json` contiene tre parametri:

| Parametro     | Descrizione                                    |
|---------------|------------------------------------------------|
| `vtiger_url`  | URL completo dell'istanza Vtiger CRM           |
| `username`    | Username per l'autenticazione Webservice       |
| `access_key`  | Access Key (da Vtiger: My Preferences > Access Key) |

Esempio:

```json
{
    "vtiger_url": "https://miocrm.example.com/vtigercrm/",
    "username": "admin",
    "access_key": "la-tua-access-key"
}
```

Per trovare l'Access Key in Vtiger: vai in **My Preferences** (icona utente in alto a destra) e copia il valore del campo **Access Key**.

---

## Requisiti

- PHP >= 7.0
- Estensione `ext-json`

---

## Licenza

MIT - [javanile/vtiger-client](https://github.com/javanile/vtiger-client)