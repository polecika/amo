<!DOCTYPE>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <!-- Начало скрипта  создания контактов, компаний, сделок, покупателей и связей между ними-->
        <p>Добавление сущностей и связей:</p>
        <p>
            <small>Создать N(от 0 до 10 000, вводит пользователь) контактов, компаний, сделок, покупателей и связи между ними;
                создать поле мультисписок из 10ти значений для контактов и установить каждому контакту свой случайный набор значений;
            </small>
        </p>
        <form method="POST" action="result.php?func=1">
            <div class="input-group">
                <label>Введите число:</label>
                <input type="number" name="number" min="0" max="10000">
            </div>
            <div>
                <input type="submit" value="Создать"/>
            </div>
        </form>
        <hr />
        <!-- Конец скрипта -->

        <!-- Начало скрипта по добавлению поля типа "текст"-->
        <p>Добавление поля типа "Текст"</p>
        <p>
            <small>Добавить значение дополнительного поля текст по id элемента сущности. Если у сущности уже есть поле текст, изменить его.
            </small>
        </p>
        <form method="POST" action="result.php?func=2">
            <div class="input-group">
                <label>ID сущности:</label>
                <input name="id" type="text" required />
            </div>
            <div class="input-group">
                <label>Тип сущности:</label>
                <select name="entity_type" required>
                    <option value="1">Контакт</option>
                    <option value="2">Сделка</option>
                    <option value="3">Компания</option>
                    <option value="12">Покупатель</option>
                </select>
            </div>
            <div class="input-group">
                <label>Значение:</label>
                <input name="text" type="text" />
            </div>
            <div>
                <input type="submit" value="Добавить" required />
            </div>
        </form>
        <hr />
        <!-- Конец скрипта -->

        <!-- Начало скрипта по созданию примечания -->
        <p>Добавление примечания</p>
        <p>
            <small>
                Укажите ID элемента сущности, в которую хотите добавить примечание.
            </small>
        </p>
        <form method="POST" action="result.php?func=3">
            <div class="input-group">
                <label>ID сущности:</label>
                <input name="id" type="text" required />
            </div>
            <div class="input-group">
                <label>Тип сущности:</label>
                <select name="entity_type" required>
                    <option value="1">Контакт</option>
                    <option value="2">Сделка</option>
                    <option value="3">Компания</option>
                    <option value="12">Покупатель</option>
                </select>
            </div>
            <div class="input-group">
                <p>
                    <label>Тип примечания:</label>
                    <input type="radio" name="note_type" value="4" required /> Обычное примечание
                    <input type="radio" name="note_type" value="10" required />  Входящий звонок
                </p>
            </div>
            <div class="input-group">
                <label>Текст примечания:</label>
                <input type="text" name="text" required />
            </div>
            <div>
                <input type="submit" value="Создать"/>
            </div>
        </form>
        <hr />
        <!-- Конец скрипта -->

        <!-- Начало скрипта по добавлению задачи -->
        <p>Добавление задачи</p>
        <p>
            <small>
                Добавить задачу в элемент указанной по ID сущности.
            </small>
        </p>
        <form method="POST" action="result.php?func=4">
            <div class="input-group">
                <label>ID сущности:</label>
                <input type="text" name="id" required />
            </div>
            <div class="input-group">
                <label>Тип сущности:</label>
                <select name="entity_type" required>
                    <option value="1">Контакт</option>
                    <option value="2">Сделка</option>
                    <option value="3">Компания</option>
                    <option value="12">Покупатель</option>
                </select>
            </div>
            <div class="input-group">
                <label>Дата дедлайна:</label>
                <input type="datetime-local" name="date" required />
            </div>
            <div class="input-group">
                <label>ID ответственного:</label>
                <input type="text" name="id_main_user" value="" required />
            </div>
            <div class="input-group">
                <label>Текст задачи:</label>
                <input type="text" name="text" required />
            </div>
            <div>
                <input type="submit" value="Добавить"/>
            </div>
        </form>
        <hr />
        <!-- Конец скрипта -->

        <!-- Начало скрипта по завершению задачи по указанному ID -->
        <p>Завершение задачи</p>
        <p>
            <small>
                Укажите ID задачи, которую хотите завершить.
            </small>
        </p>
        <form method="POST" action="result.php?func=5">
            <div class="input-group">
                <label>ID задачи:</label>
                <input type="text" name="id" />
            </div>
            <div>
                <input type="submit" value="Удалить"/>
            </div>
        </form>
        <hr />
        <!-- Конец скрипта -->
    </body>
</html>
