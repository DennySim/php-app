# Описание приложения
### Для развертывания приложения использованые следующие открытые ресурсы:
### - функционал frontend + backend, images:
 - nginx
 - allansimon/php7-fpm-postgresql
### - готовая реализация репликации postgres primary-replica (helm chart)
- registry.developers.crunchydata.com/crunchydata/crunchy-postgres/ubi8-14.4-0  
### - выполнение миграции (создание таблицы blacklisted), image:
- jbergknoff/postgresql-client


### В чарт primary-replica добавлены следующие шаблоны:
  - nginx-deployment.yaml - развертывание frontend + backend
  - ingress.yaml - развертывание ingress

### Также в уже имеющийся файл чарта primary-pod.yaml добавлена job на создание таблицы blacklisted c полями location,ip_address,datetime

# Установка приложения в кластере kubernetes 

## 1) Установить в кластере kubernetes haproxy-ingress-controller  
Документация по установке https://haproxy-ingress.github.io/docs/getting-started/

### - Установить репозиторий
 - helm repo add haproxy-ingress https://haproxy-ingress.github.io/charts  

### - Создать файл haproxy-ingress-values.yaml с параметрами:
'''
controller:
  hostNetwork: true
'''

### - Установить haproxy-ingress-controller:
helm install haproxy-ingress haproxy-ingress/haproxy-ingress\
  --create-namespace --namespace ingress-controller\
  --version 0.14.0-beta.1 --devel\
  -f haproxy-ingress-values.yaml


## 2) Скопировать каталог data в каталог / на worker-node

## 3) Развертывание приложения кластере kubernetes

- задать имя хост, 
  параметр hostname в values.yaml (используется в ingress.yaml)

- развернуть приложение 
helm install primary-replica primary-replica


## 4)  Проверка работы в браузере
   
  - http://hostname/?n=x
  - http://hostname/blacklisted
