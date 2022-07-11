# log_parser

# Задача:
Требуется написать PHP скрипт, обрабатывающий лог файл и выдающий информацию о нём в json виде.

# Формат вывода
{
  views: 16,
  urls: 5,
  traffic: 187990,
  crawlers: {
      Google: 2,
      Bing: 0,
      Baidu: 0,
      Yandex: 0
  },
  statusCodes: {
      200 : 14,
      301 : 2
  }
}

# Запуск
php parser.php --log_file=./access_log
