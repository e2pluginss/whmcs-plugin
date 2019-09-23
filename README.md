# WHMCS Plugin for White Label VPN Reseller Accounts

This is an example WHMCS Provisioning plugin for White Label VPN (WLVPN) Resellers.

| NAME | WEBSITE |
| ------ | ------ |
| WLVPN | [https://www.wlvpn.com] |
| WHMCS | [https://www.whmcs.com] |

This plugin enables WLVPN's resellers to manage users' account in WHMCS control
panel directly.

This plugin will manage the following user action automatically from WHMCS:

  - Adding customers
  - Updating customers (e.g. reseting your VPN password)
  - Suspending customers
  - Terminating customers
  - Renewing customers


## Prerequisites

This plugin is built for WHMCS `version 7.7.1` or newer only. Backward
compatibility has not been tested.

You should have the WHMCS installed and running in your server
(https://docs.whmcs.com/Getting_Started).


## Getting Started

* Copy the `wlvpn` directory from thsi repo into the `modules/servers`
* directory inside your WHMCS installation.

Here would be the file structures would look like:
 
```bash
WHMCS root directory
    └── modules/
        └── servers/
            └── wlvpn/  # from this repository
                ├── hooks.php
                ├── whmcs.json
                ├── wlvpn.php
                └── templates/
                    └── *.tpl
```

* To make use of this plugin, when creating a new product in your WHMCS:

  - In the "Details" tab
    - Ensure "Require Domain" is unchecked
  -  In the "Module Settings" tab
    - Select **`White Label VPN Resellers`** from the "Module Name" dropdown
    - Enter the `API Key`, `Group ID` and `API Endpoint` as provided by us.
      - `API Key` - Please contact your account representative
      - `Group ID` - Obtained from your WLVPN reseller dashboard
      - `API Endpoint` - See https://docs.wlvpn.com/ (e.g. `https://api.wlvpn.com/`)
    - Ensure you select the option to setup the product on payment receipt.

![Screenshot](Screenshot.png?raw=true "Optional Title")


## License

This project is licensed under the MIT License. Please see the
[LICENSE](LICENSE) for more details.


## Question

If you have any questions, please check the WLVPN FAQ https://wlvpn.com/faqs/ or
contact us at https://wlvpn.com/#contact
