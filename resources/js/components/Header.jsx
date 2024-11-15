export default function Header() {
    return (
        <div className="header">
            <div className="header-content">
                <nav className="navbar navbar-expand">
                    <div className="collapse navbar-collapse justify-content-between">
                        <div className="header-left">
                            <div className="input-group search-area right d-lg-inline-flex d-none">
                                <input type="text" id="searchField" name="search" required className="form-control srch"
                                       placeholder="Поиск..."/>
                                <div className="input-group-append">
                                    <span className="input-group-text">
                                        <button className="btn btn-sm" type="submit">
                                            <i className="flaticon-381-search-2"/>
                                        </button>
                                    </span>
                                </div>
                                <div className="input-group-append">
                                    <div className="input-group-text">
                                        <select name="type" id="searchTypeField" className="form-control-sm">
                                            <option value="card">Товар</option>
                                            <option value="order">Заказ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul className="navbar-nav header-right main-notification">
                            <li className="nav-item dropdown header-profile">
                                <a className="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <div className="header-info">
                                        <span className='Profile'>Profile</span>
                                    </div>
                                </a>
                                <div className="dropdown-menu dropdown-menu-right">
                                    <a href={"/settings/changeSeller/#"} className="dropdown-item ai-icon">
                                        <span className="ml-2">seller name</span>
                                    </a>
                                    <a href={"/login/logout"} className="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns={"https://www.w3.org/2000/svg"}
                                             className="text-danger"
                                             width="18" height="18" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor"
                                             strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                            <polyline points="16 17 21 12 16 7"/>
                                            <line x1="21" y1="12" x2="9" y2="12"/>
                                        </svg>
                                        <span className="ml-2">Выход </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    )
}
