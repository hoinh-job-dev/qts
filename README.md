# PHP Codeigniter Framework ICO for Project Quanta Token Sales

## Features

- CURD user Operator, Agent, Client
- Operator manage Agent and Client sale Token QNT
- Create Agent with commission 20%, 15%, 10%
- Order Token QNT from USD or BTC
- Validation & business logic

## Tech
- PHP Framework Codeigniter
- MySQL
- Apache2

## API Operator 
POST /QT/Operator/login
GET /QT/Operator/viewPersonal
POST /QT/Operator/reOrderConfirm
POST /QT/Operator/inputBanking
POST /QT/Operator/inputExchangedBtc
GET /QT/Operator/viewBankBtc
POST /QT/Operator/makeToken
POST /QT/Operator/makeClosedOrder
GET /QT/Operator/viewApprovedToken
GET /QT/Operator/viewCommissions
GET /QT/Operator/viewRefunds
GET /QT/Operator/home
POST /QT/Operator/orderlist
POST /QT/Operator/receiptIssueToken
POST /QT/Operator/listCommissions
POST /QT/Operator/activitylist
GET /QT/Operator/viewRate
POST /QT/Operator/insertRate
POST /QT/Operator/linkAgent
POST /QT/Operator/operatorManagement
POST /QT/Operator/curdOperatorManagement
POST /QT/Operator/reissueToken
POST /QT/Operator/uploadDocs
POST /QT/Options/index
POST /QT/Operator/changePasswd
POST /QT/Operator/resendEmail
POST /QT/Operator/editTokenList
POST /QT/Operator/issueRedeemToken 
POST /QT/Operator/logout

## API Agent
POST Agent/login
POST Agent/complete
POST Agent/home  
POST Agent/linkAgent      //create Agent 15% commission Agent 10% commision
GET Agent/guideAgent
POST Agent/confirmGuideAgent
POST Agent/makeAgentLink
POST Agent/linkClient
POST Agent/makeClientLink
GET Agent/viewClients
GET Agent/viewCommission
GET Agent/viewLinks
POST Agent/logout

## API Client
POST Client/login
GET Client/home
POST Client/quantaWallet
POST Client/redeemInfo
POST Client/completeOrder
POST Client/addOrder
GET Client/viewBtcAddr
GET Client/viewToken
POST Client/getUsdBtcRate
GET Client/viewBtcAddr/$1
GET Client/viewBtcAddr/$1/$2
POST Client/icoFinish
POST Client/regAccount/$1
