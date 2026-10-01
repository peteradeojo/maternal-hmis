const StatCard = ({ children, border = 'border-blue-500', color = 'text-blue-500', title, icon, footer }) => {
    return <div className={`bg-white rounded-lg shadow p-6 border-l-4 ${border}`}>
        {title && <span className={`${color}`}>{title}</span>}

        {icon && <div className="">{icon}</div>}

        <div>{children}</div>

        {footer && <div className="mt-4">
            <span className={`text-${color} hover:text-${color} font-medium`}>
                {footer}
            </span>
        </div>}
    </div >
}

export default StatCard;
