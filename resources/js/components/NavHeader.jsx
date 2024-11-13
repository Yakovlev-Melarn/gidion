export default function NavHeader() {
    return (
        <div className="nav-header">
            <a href="/" className="brand-logo justify-content-center">
                <h4>Gidion <small>(react beta)</small></h4>
            </a>
            <div className="nav-control">
                <div className="hamburger">
                    <span className="line"/><span className="line"/><span className="line"/>
                </div>
            </div>
        </div>
    )
}
