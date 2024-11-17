# MS_TEST_CASE

- [Project Setup](#project-setup)
- [API Request for SMS Confirmation](#api-request-for-sms-confirmation)
- [Validation measures and spam prevention flow](#validation-measures-and-spam-prevention-flow)
- [More about rate limiters](#more-about-rate-limiters)
- [My recommendations on how would you make this service more fault-tolerant (Bonus)](#my-recommendations-on-how-would-you-make-this-service-more-fault-tolerant-bonus)
- [Test case summaries](#test-case-summaries)
- [How long the task took](#how-long-the-task-took)


---

# Project Setup

- Clone the repository
- **Make sure you have the following ports free: 80, 443, 3306, 6379.**
### Up docker containers
````
docker compose up -d
````
### Install composer dependencies:
````
docker exec -it ms_test_case-php composer install
````
### Run database migrations:
````
docker exec -it ms_test_case-php bin/console doctrine:migrations:migrate
````
### Load data fixtures:
````
docker exec -it ms_test_case-php bin/console doctrine:fixtures:load
````
### Consume sms service messages:
````
docker exec -it ms_test_case-php bin/console messenger:consume async
````

---

# API Request for SMS Confirmation

This document describes how to send an HTTP POST request to confirm SMS verification for a user.

### Endpoint
````
POST http://localhost:80/api/v1/auth/confirmation/sms
````

### Request Headers

Make sure to include the following header in your request:
- Content-Type: application/json"

### Request Body

The body of the request must be in JSON format and should contain the valid `phoneNumber` field.

Example request body:

```json
{
  "phoneNumber": "+380981111111"
}
```

The endpoint accepts only predefined phone numbers.
You can follow next phone numbers which you have loaded by data fixtures:
````
docker exec -it ms_test_case-php bin/console doctrine:fixtures:load
````
```json
[
  "+380981111111",
  "+380981111112",
  "+380981111113",
  "+380981111114",
  "+380981111115",
  "+380981111116",
  "+380981111117",
  "+380981111118",
  "+380981111119",
  "+380981111110"
]
```

### Responses

#### Success response
If you have sent a valid phone number you will get the next response:


```
HTTP 200 OK
Content-Type: application/json
```
```json
{
    "message": "We will send the SMS code as soon as possible."
}
```

#### Invalid phone number format response
If you have sent an invalid phone number you will get the following response:
```
HTTP 422 Unprocessable Content
Content-Type: application/json
```
```json
{
  "errors": ["This value is not a valid phone number."]
}

```

#### Not contain `phoneNumber` field response
If your request body does not contain the `phoneNumber` field you will get the following response:

```
HTTP 422 Unprocessable Content
Content-Type: application/json
```
```json
{
  "errors": ["The \"phoneNumber\" field is required."]
}
```

#### Invalid request body format
If you have sent no `Content-Type: application/json` you will get the next response:
```
HTTP 415 Unsupported Media Type
Content-Type: application/json
```
```json
{
  "errors": ["Unsupported format."]
}
```

---

# Validation measures and spam prevention flow
I accept only predefined phone numbers. I follow the next flow:
1. We should send SMS messages with confirmation code to log in or register actions.
So if a user can to log in he has already registered and we save his phone number in a database.
But I propose saving phone numbers when a user has started the registration process with a `PENDING` trust status.
In this case, we send SMS codes only to phone numbers that have interacted with our system.
2. A user sends a request and we validate it. If the user request is invalid we send error responses.
To validate phone numbers I use `libphonenumber` by Google (https://github.com/giggsey/libphonenumber-for-php.git).
3. We detect if the user is banned using the custom `#[DetectBannedUser]` attribute.
If the user is banned we return the next response:
```
HTTP 422 Unprocessable Content
Content-Type: application/json
```
```json
{
    "errors": ["Banned by user account, phone number"]
}
```
4. Users can be banned by user account, phone number, and IP address.
5. Rate limits and user historical activity analysis. Using the custom attributes below, we set rate limits.
When these limits are exhausted, we start detecting suspicious activity.
From that moment, I save unique identifiers in Redis and begin analyzing the activity.
If the user continues sending requests while the time limit is still active
and sends three additional requests after this trigger, I ban the user.
```php
#[PhoneNumberRateLimiter(limit: 1, interval: '1 minute', amount: 1)]
#[UserAgentRateLimiter(limit: 10, interval: '1 minute', amount: 10)]
#[ClientIpRateLimiter(limit: 100, interval: '1 minute', amount: 100)]
```
6. At this stage, we have fully validated the user activity
and we are sending an SMS message asynchronously using Redis messenger

---

# More about rate limiters
### #[PhoneNumberRateLimiter(limit: 1, interval: '1 minute', amount: 1)]
When we send an SMS the user can request the next one after one minute.

### #[UserAgentRateLimiter(limit: 10, interval: '1 minute', amount: 10)]
The user can attempt to send a maximum of 10 requests to send an SMS code from a single device.

### #[ClientIpRateLimiter(limit: 100, interval: '1 minute', amount: 100)]
To prevent spam farm attacks, we allow up to 100 requests per minute from a single IP address.

---

# My recommendations on how would you make this service more fault-tolerant (Bonus)
1. Users can change their phone numbers, so it is necessary
to check bans separately for the user and the phone number.
Over time, the phone number's ownership might be changed, and it may be necessary to unblock the number only.
To prevent users from bypassing our spam prevention measures by frequently changing phone numbers,
we also ban the associated phone number when a user account is banned.

2. Determine the address by IP. If the user changes cities or countries within 15 minutes
and makes more than 3 identical requests, we ban it.

3. Add CAPTCHA service on the client side to prevent bot requests.

4. Spam services may use well-known User Agents. We need to identify such User Agents and add them to the filter.
If an HTTP request fails to pass the filter, we block the user.

5. To log bans I use a polymorphic table "ban_log".
We should add MySQL triggers to the users, phone_number, and client_ip tables to improve database consistency

# Test case summaries

At least one implementation:
- Validate by user info (Done!)
- Rate limiting (Done!)
- Historical analyse (Done!)

Bonus (My own recommendations) (Done!)

# How long the task took
I worked for 3 days