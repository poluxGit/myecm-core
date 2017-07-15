# Dockers Management for myecm dev.

## Dockers List

- For API  : docker run  polux/web-srv:dev_api
- For Web : docker run  polux/web-srv:dev

## Usefull commands

**Builds Commands**

```
# For API
docker build -t polux/web-srv:dev_api -f ./main.dockerfile .
```


```msdos
# For Web
docker build -t polux/web-srv:dev -f ./df-images-php7-apache2.dockerfile .
```
