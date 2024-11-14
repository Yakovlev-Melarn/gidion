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
                    </div>
                </nav>
            </div>
        </div>
    )
}
