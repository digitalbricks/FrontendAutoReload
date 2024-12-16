# CHANGELOG

## 0.0.5 (16.12.2024)
- fixed bug where excluded folders were not being excluded in the directory iterator; by using a custom RecursiveFilterIterator

## 0.0.4 (29.11.2024)
- removed a check for $input->post wich may lead to configuration not reflected correctly
- called getConfigFromPost() earlier in init() method, before return

## 0.0.3 (22.10.2024)
- added `requires` in module info, specifying PW>=3.0.173 and PHP>=8.2.0

## 0.0.2 (21.10.2024)
- conditional module autoloading (frontend only)
- automatic polling stop if there is an invalid response from endpoint

## 0.0.1 (11.10.2024)
- initial release