# Интерактивные карты (бета)

### Ручной запуск docker-compose

>Для запуска или остановки проекта необходимо выполнить команду в консоли в папке с проектом

###### В production
```sh
docker-compose up --build -d
```

###### В development
```sh
docker-compose -f docker-compose.yml -f docker-compose-dev.yml up --build -d
```

* флаг `--build` будет заново собирать проект;
* флаг `-d` — это сокращение для `--detach`. Эта команда запускает контейнер в фоновом режиме. Это позволяет использовать терминал, из которого запущен контейнер, для выполнения других команд во время работы контейнера;
* флаг `-f` позволяет указать другой файл конфигурации `docker-compose.yml`. Все файлы будут склеены в один `.yml` файл.

###### Остановка контейнера
```sh
docker-compose down
```

###### Для просмотра логов в консоли
```sh
docker-compose logs -f
```

###### Выход из логов:
```
ctrl+z
```
