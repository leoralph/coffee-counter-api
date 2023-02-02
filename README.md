### COFFEE COUNTER

Coffee counter is an API that can create users, register the amount of coffee dranked by them, and retrieve their counters, it also has a ranking of the leading conffee drinkers.

It was developed using only PHP, without composer or any external libraries.

#### Endpoints:

---

`POST /users`

Creates an user.

|       Name | Required |  Type  | Description                                   |
| ---------: | :------: | :----: | --------------------------------------------- |
|     `name` | required | string | User name                                     |
|    `email` | required | string | User email, it will be used for logging in    |
| `password` | required | string | User password, it will be used for logging in |

---

`POST /login`

Makes login, then returns a token for all subsequent requests.

|       Name | Required |  Type  | Description   |
| ---------: | :------: | :----: | ------------- |
|    `email` | required | string | User email    |
| `password` | required | string | User password |

---

`GET /users`

Retrieves all users info.

---

`GET /users/{userId}`

Retrieves an user info.

|     Name | Required | Type | Description |
| -------: | :------: | :--: | ----------- |
| `userId` | required | int  | The user id |

---

`PUT /users/{userId}`

Updated the specified user using the posted data.

|       Name | Required |  Type  | Description       |
| ---------: | :------: | :----: | ----------------- |
|     `name` | optional | string | New user name     |
|    `email` | optional | string | New user email    |
| `password` | optional | string | New user password |

---

`DELETE /users/{userId}`

Deletes the specified user.

|     Name | Required | Type | Description |
| -------: | :------: | :--: | ----------- |
| `userId` | required | int  | The user id |

---

`POST /users/{userId}/drink`

Register the certain amount of coffee dranked by the specified user.

|     Name | Required | Type | Description               |
| -------: | :------: | :--: | ------------------------- |
| `userId` | required | int  | The user id               |
| `amount` | required | int  | The amount of coffee cups |

---

`GET /users/{userId}/history`

Returns the user coffee drinking history.

|     Name | Required | Type | Description |
| -------: | :------: | :--: | ----------- |
| `userId` | required | int  | The user id |

---

`GET /users/ranking`

Returns the ranking of the users that dranked most coffee, filter can be by last X days, of a specific date.

|        Name | Required |    Type    | Description                          |
| ----------: | :------: | :--------: | ------------------------------------ |
| `last_days` | optional |    int     | Send this to filter by last X days   |
|      `date` | optional | date:Y-m-d | Send this to filter by specific date |

---
