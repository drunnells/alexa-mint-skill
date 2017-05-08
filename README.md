# alexa-mint-skill v0.3

Unofficial simple Alexa skill to collect and speak balances from Mint (http://mint.com)

Dustin Runnells (dustin@runnells.name)

*THIS SKILL IS FOR PERSONAL USE ONLY AND IS NOT ENDORSED BY MINT OR AMAZON - DO NOT SUBMIT THIS TO AMAZON FOR CERTIFICATION*

### Status
It seems to be working for me. I can get a balance on my checking accounts, credit cards and mortgage through different banks by speaking things like:
* Alexa, ask Mint for my bank balance.
* Alexa, ask Mint for my balances.
* Alexa, ask Mint for my credit card balance.

Someday I'd like to add budgets to this script as well.

### Notes
* This skill is intended to be used for personal/private use. It cannot be installed from the Alexa Skills Market. Installation of this skill requires you to follow the steps that a Skill developer would need to take to create a skill for development purposes. After completing these steps you will be able to ask Alexa for bank balance information obtained from your Mint account.
* Since this will be a development skill on your Amazon account, it will only work if your Amazon Echo device is associated with the same account that you are adding this skill to. It should appear in the Alexa app on your phone under Skills->Your Skills without any special steps needed to enable.
* This skill is based on the [Alexa PHP Helo World example by Mell L Rosandich](http://www.ourace.com/145-amazon-echo-alexa-with-php-hello-world)

## Dependancies
* Linux/Unix web server capable of SSL/HTTPS with PHP installed to host the PHP Alexa skill
* [MintAPI](https://github.com/mrooney/mintapi) CLI tool by Mike Rooney

## Installation

1. Install MintAPI and verify that it works on the command line.
2. Obtain an SSL certificate (https://letsencrypt.org/ is free). This is required for all Alexa skills.
3. Configure your web server to host an https website and copy/host all project files at that URL (eg. https://example.com/alexa-mint-skill)
4. Edit the *config.php* file to include:
    * Your Mint email address
    * Your Mint password
    * The ius_session for MintAPI (See [MintAPI](https://github.com/mrooney/mintapi) for more info)
    * The thx_guid required for MintAPI. (See [MintAPI](https://github.com/mrooney/mintapi) for more info)
    * The correct path to mintapi
5. Login to the Alexa section of the Amazon development portal: https://developer.amazon.com/edw/home.html#/
6. Click "**Get Started**" in the *Alexa Skills Kit* box.
7. Click the "**Add New Skill**" button on the top Right.
8. Set Skill Type to "**Custom**".
9. Set *Name* to "**Mint**".
10. Set *Invocation Name* to "**mint**".
11. Set *Audio Player* to "**No**".
12. Click **Next** to move to the *Interaction Model* tab.
13. Paste the following into the *Intention Schema* box:
```json
{
  "intents": [
    {
      "slots": [
        {
          "name": "bankType",
          "type": "BANK_OBJECT"
        },
        {
          "name": "budgetType",
          "type": "BUDGET_OBJECT"
        },
        {
          "name": "actionType",
          "type": "ACTION_OBJECT"
        }
      ],
      "intent": "Mint"
    }
  ]
}
```
14. Add a *Custom Slot Type* for "**BANK_OBJECT**" wth the values:
* bank
* loan
* investment
* credit
* credit card

15. Add a *Custom Slot Type* for "**BUDGET_OBJECT**" with the values:
* food
* shopping
* bills
* entertainment
* gas
* auto

16. Add a *Custom Slot Type* for "**ACTION_OBJECT**" with the values:
* budgets
* balances

17. Paste the below into the *Sample Utterances* box:
```
Mint get {bankType} balance
Mint get {actionType}
Mint get {budgetType} budget
Mint get my {bankType} balance
Mint get my {actionType}
Mint get my {budgetType} budget
Mint for my {bankType} balance
Mint for my {actionType}
Mint for my {budgetType} budget
Mint for the {bankType} balance
Mint for the {actionType}
Mint for the {budgetType} budget
```

18. Click the **Next** button to move on to the *Configuration* tab.
19. Set *Service Endpoint Type* to **HTTPS**.
20. In the text box under the region enter the HTTPS URL where you are hosting the alexa.php file from this project (eg. https://example.com/alexa-mint-skill/alexa.php ).
21. Click the **Next** button to move to the *SSL Certificate* tab.
22. Select "**My development endpoint has a certificate from a trusted certificate authority**".
23. Click **Next** to move to the *Test* tab. 
24. Type "*Mint get my bank balance*" in the simulator and confirm that no obvious errors appear in the returned JSON.
25. Try on your Amazon Echo. If all went well, you should now be able to ask Alexa to retrieve your bank balances.

### Troubleshooting

Make sure that the MintAPI CLI tool is working with your account. This skill will execute the command as follows based on the configuration that you provided in config.php:

```
mintapi --session <ius_session> --thx_guid <thx_guid> <mint email address> <mint password>
```

### Development

Want to contribute? Let me know! This is my first GitHub project, I'd love to hear how to make it better.


License
----

MIT
