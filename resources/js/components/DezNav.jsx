export default function DezNav() {
    return(
        <div className="deznav">
            <div />
            <div className="deznav-scroll">
                <ul className="metismenu" id="menu">
                    <li><a className="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i className="flaticon-091-shopping-cart"/>
                        <span className="nav-text">Магазин</span>
                    </a>
                        <ul aria-expanded="false">
                            <li><a href="#">Заказы</a></li>
                            <li><a href="#">Остатки</a></li>
                        </ul>
                    </li>
                    <li><a className="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i className="flaticon-077-menu-1"/>
                        <span className="nav-text">Товары</span>
                    </a>
                        <ul aria-expanded="false">
                            <li><a href="#">Все товары</a></li>
                            <li><a href="#">Копирование карточки</a></li>
                            <li><a href="#">Товары конкурентов</a></li>
                            <li><a href="#">Каталоги поставщиков</a></li>
                            <li><a href="#">Удалить товары</a></li>
                        </ul>
                    </li>
                    <li><a className="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i className="flaticon-381-network"/>
                        <span className="nav-text">Утилиты</span>
                    </a>
                        <ul aria-expanded="false">
                            <li><a href="#">Календари</a></li>
                        </ul>
                    </li>
                    <li><a className="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i className="flaticon-073-settings"/>
                        <span className="nav-text">Настройки</span>
                    </a>
                        <ul aria-expanded="false">
                            <li><a href="#">Магазины</a></li>
                            <li><a href="#">Поставщики</a></li>
                            <li><a href="#">Конкуренты</a></li>
                            <li><a href="#">Фоновые процессы</a></li>
                        </ul>
                    </li>
                </ul>
                <div className="copyright">
                    <p><strong>Gidion Seller Portal</strong> © 2024 All Rights Reserved</p>
                </div>
            </div>
        </div>
    )
}
