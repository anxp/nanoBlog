# nanoBlog

### Окно аутентификации в админ-панели и механизм его работы:

* Пользователь заходит на /admin/index.php, вводит логин и пароль.
* Пароль хешируется (простой ф-ей на JS, надо заменить на что-то более серьезное)
* Хеш вместе с логином преобразуется в JSON строку и через Ajax отправляется на обработчик - loginchecker.php.
* Обработчик сверяется с базой, формирует ответ в виде JSON строки, и отправляет его браузеру.
* Если ответ положительный, сервер также отправляет адрес перехода (adminview.php). JS в окне браузера находясь на странице index.php
инициирует переход на страницу adminview.php. После перехода на которую первым делом проверяется чтобы $_SESSION['role'] == 'admin', и если это не так,
пользователь перебрасывается обратно на index.php.
* Если ответ отрицательный (не аторизировано) в браузер просто отправляется этот отрицательный ответ, на основе которого JavaScript встряхивает
окно логина, сбрасывает логин/пароль тем самым намекая попробовать еще раз.

### Редактор записи/статьи

В редакторе можно вводить заголовок, тело записи, выбирать категорию (список загружается с БД при формировании страницы на сервере),
выбирать статус - опубликовано/черновик. **Редактор реализует главную мечту блоггера - никогда не терять несохраненный текст,
что бы ни случилось**. Если пользователь забыл указать какой-либо обязательный компонент записи (а именно **заголовок**, **категорию**, **тело записи**), весь его введенный текст не будет потерян! Сохранение черновика реализовано через массив $_SESSIONS,
и при какой-либо пользовательской ошибке обработчик (postController.php) сообщает текст ошибки, и возвращает назад через $_SESSION всю введенную
пользователем информацию. Чтобы удалить черновик (если он не нужен), пользователь должен явно указать это щелкнув соответствующую ссылку/кнопку.

Кроме создания новых записей, можно редактировать уже существующие.

Одним словом из CRUD реализовано CRU.

К записи можно добавлять ключевые слова, разделяемые запятой. Регулярное выражение на стороне сервера проверяет строку ключевых слов на базовый синтаксис
(чтоб были разделены запятой, не содержали левых символов, и чтоб не начинались и не заканчивались на запятую) и в случае несоответствия шаблону
возвращает пользователю введенную строку, с соответствующим сообщением об ошибке.

Также, к записи можно добавлять картинку. Для картинки при успешной загрузке **генерируется иконка**. При запросе на редактирование записи к которой прикреплена
картинка, выводится уведомление, что картинка у этой записи уже есть, но при желании пользователь может заменить ее другой, просто загрузив новую картинку.

### Пагинация
Пагинация пока реализована обычная (без плавно появляющегося центрального блока), но удобная.
Также, постраничная разбивка выводит заголовки сохраненных записей разным цветом - зеленые опубликованы, красные - нет (в админке).

### Живой поиск по тегам
На самом деле поиск не живой - в процессе набора запроса обращения к базе не производятся. Наоборот, при загрузке страницы, также загружается JSON со всеми
существующими ключевыми словами (отфильтрованные от пробелов, дубликатов, приведенные к нижнему регистру). Потом, после формирования DOM, из подгруженного
JSON-а JavaScript формирует скрытое меню, и добавляет его в DOM. Когда пользователь кликает на поле поиска, появляется это меню, которое по мере набирания
текста отфильтровывает не подходящие пункты и таким образом остается просто кликнуть на одном из пунктов для автодополнения начатого запроса.

К сожалению, это выпадающее JS меню не управляется клавишами вверх/вниз с клавиатуры...

### Общий функционал
Общий функционал стандартный для блога. Тип внутренних ссылок - олдскульный, на GET-параметрах, потому что делалось без MVC и без роутера. Хотя ключевая
идея MVC - отделить логику от шаблонов учтена. 

### [update from 18.09.2018]
С целью максимальной защиты от SQL иньекциии, все взаимодействия с БД переписаны с использованием PDO и подготовленных запросов.

### Обнаруженные баги
**[OPEN]** Список категорий **в админке** выводится циклом for. Эта конструкция может сломаться, если какая-то категория будет удалена, поскольку for
ожидает индексированный массив. Заменить на foreach. На представлениях которые видит пользователь этого бага уже нет (использован foreach).

**[OPEN]** Если добавить к записи картинку, сохранить запись в базе, потом открыть на редактирование, удалить например заголовок, и попытаться сохранить - 
текстовый редактор выведет соответствующее сообщение об ошибке (нет заголовка), но НЕ покажет что у этой записи есть картинка. Это происходит потому, что 
при первичной валидации на то что заполнены требуемые поля контроллер **не сверяется с базой**, и соответственно инфомация что есть картинка не выводится (временно теряется). Если
сохранить новость (пропустить через базу) то редактор опять покажет что картинка таки есть. Вариант решения - добавить в форму записи скрытое поле ImageAttached=YES
и "крутить" его пока гоняем запись туда-сюда между пользователем и валидатором.