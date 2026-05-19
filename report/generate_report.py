from pathlib import Path

from PIL import Image as PilImage
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import inch
from reportlab.platypus import (
    Image,
    KeepTogether,
    ListFlowable,
    ListItem,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)


BASE_DIR = Path(__file__).resolve().parent
PROJECT_DIR = BASE_DIR.parent
PDF_PATH = BASE_DIR / "laravel_saas_assignment_report.pdf"
SCREENSHOT_PATH = BASE_DIR / "saas-dashboard.png"
SCREENSHOT_THUMB_PATH = BASE_DIR / "saas-dashboard-preview.png"


styles = getSampleStyleSheet()
styles.add(
    ParagraphStyle(
        name="CoverTitle",
        parent=styles["Title"],
        fontName="Helvetica-Bold",
        fontSize=24,
        leading=30,
        alignment=TA_CENTER,
        spaceAfter=20,
    )
)
styles.add(
    ParagraphStyle(
        name="SectionTitle",
        parent=styles["Heading1"],
        fontName="Helvetica-Bold",
        fontSize=16,
        leading=20,
        spaceBefore=14,
        spaceAfter=8,
    )
)
styles.add(
    ParagraphStyle(
        name="SubTitle",
        parent=styles["Heading2"],
        fontName="Helvetica-Bold",
        fontSize=12,
        leading=15,
        spaceBefore=8,
        spaceAfter=5,
    )
)
styles.add(
    ParagraphStyle(
        name="BodyTextClean",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=10.5,
        leading=15,
        spaceAfter=7,
    )
)
styles.add(
    ParagraphStyle(
        name="Small",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=8.5,
        leading=11,
        textColor=colors.HexColor("#475467"),
    )
)
styles.add(
    ParagraphStyle(
        name="CodeBlock",
        parent=styles["Code"],
        fontName="Courier",
        fontSize=8.4,
        leading=11,
        backColor=colors.HexColor("#F4F6F8"),
        borderColor=colors.HexColor("#D0D5DD"),
        borderWidth=0.5,
        borderPadding=6,
        spaceBefore=4,
        spaceAfter=8,
    )
)


def p(text: str, style: str = "BodyTextClean") -> Paragraph:
    return Paragraph(text, styles[style])


def bullets(items: list[str]) -> ListFlowable:
    return ListFlowable(
        [ListItem(p(item), leftIndent=12) for item in items],
        bulletType="bullet",
        start="circle",
        leftIndent=18,
    )


def code(text: str) -> Paragraph:
    escaped = (
        text.replace("&", "&amp;")
        .replace("<", "&lt;")
        .replace(">", "&gt;")
        .replace("\n", "<br/>")
    )
    return p(escaped, "CodeBlock")


def make_screenshot_preview() -> Path | None:
    if not SCREENSHOT_PATH.exists():
        return None

    with PilImage.open(SCREENSHOT_PATH) as image:
        image = image.convert("RGB")
        width, height = image.size
        crop_height = min(height, int(width * 0.72))
        preview = image.crop((0, 0, width, crop_height))
        preview.thumbnail((1400, 920))
        preview.save(SCREENSHOT_THUMB_PATH)

    return SCREENSHOT_THUMB_PATH


def build_report() -> None:
    doc = SimpleDocTemplate(
        str(PDF_PATH),
        pagesize=A4,
        rightMargin=0.65 * inch,
        leftMargin=0.65 * inch,
        topMargin=0.6 * inch,
        bottomMargin=0.6 * inch,
        title="Laravel SaaS Assignment Report",
    )

    story = []

    story.append(p("Converting a Laravel Internal Messaging System into a Cloud SaaS Application", "CoverTitle"))
    story.append(Spacer(1, 0.15 * inch))
    story.append(p("<b>Student Name:</b> ______________________________"))
    story.append(p("<b>Student ID:</b> ______________________________"))
    story.append(p("<b>Course:</b> ______________________________"))
    story.append(p("<b>Instructor:</b> ______________________________"))
    story.append(p("<b>Project:</b> Laravel Multi-Tenant SaaS Messaging System"))
    story.append(p("<b>Date:</b> May 13, 2026"))
    story.append(Spacer(1, 0.25 * inch))
    story.append(
        p(
            "This report explains, step by step, how a normal Laravel internal messaging project was changed into a basic SaaS application where multiple companies can subscribe, use the same application, and keep their data separated."
        )
    )

    story.append(PageBreak())

    story.append(p("1. Original Project", "SectionTitle"))
    story.append(
        p(
            "The starting point was a simple Laravel application for internal messaging. It had users, messages, a message form, an inbox, sent messages, and the ability to mark a message as read."
        )
    )
    story.append(bullets([
        "Framework: Laravel 13.",
        "Database: SQLite for local development.",
        "Main feature: internal messages between users.",
        "Main files before SaaS conversion: Message model, MessageController, messages migration, and one Blade view.",
    ]))

    story.append(p("2. SaaS Goal", "SectionTitle"))
    story.append(
        p(
            "SaaS means Software as a Service. Instead of installing a separate project for every company, one Laravel application is hosted and many companies use it through subscriptions."
        )
    )
    story.append(bullets([
        "Each company is a tenant.",
        "Each tenant has its own users.",
        "Each tenant has its own messages.",
        "Each tenant has its own subscription.",
        "A Super Admin can manage companies, subscription state, and enabled modules.",
    ]))

    story.append(p("3. Research: Multi-Tenant and Multi-Modules Packages", "SectionTitle"))
    story.append(
        p(
            "Laravel supports this kind of architecture through community packages. In this assignment, the packages were researched and the same concepts were implemented manually in a simple way."
        )
    )
    package_table = Table(
        [
            ["Concept", "Package", "Purpose"],
            ["Multi-tenant", "stancl/tenancy", "Automatic multi-tenancy for Laravel, with Laravel 13 support."],
            ["Multi-tenant", "spatie/laravel-multitenancy", "Makes Laravel tenant-aware and can work with one or multiple databases."],
            ["Multi-modules", "nwidart/laravel-modules", "Organizes features into separate modules with routes, models, views, migrations, and tests."],
        ],
        colWidths=[1.25 * inch, 1.85 * inch, 3.6 * inch],
    )
    package_table.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#172033")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 8.7),
        ("GRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#D0D5DD")),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
        ("LEFTPADDING", (0, 0), (-1, -1), 6),
        ("RIGHTPADDING", (0, 0), (-1, -1), 6),
        ("TOPPADDING", (0, 0), (-1, -1), 6),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
    ]))
    story.append(package_table)
    story.append(Spacer(1, 0.12 * inch))
    story.append(p("References: https://packagist.org/packages/stancl/tenancy, https://spatie.be/docs/laravel-multitenancy, https://laravelmodules.com/docs/13/getting-started/introduction", "Small"))

    story.append(p("4. Chosen Implementation Approach", "SectionTitle"))
    story.append(
        p(
            "For this assignment, the SaaS idea was implemented using single-database multi-tenancy. This means all companies share the same database, but every company-owned record stores a company_id. The application always filters records using company_id."
        )
    )
    story.append(code("Message::where('company_id', $currentCompanyId)->get();\nUser::where('company_id', $currentCompanyId)->get();"))
    story.append(
        p(
            "This is a simple and clear way to demonstrate the SaaS concept without requiring separate databases, subdomains, or payment gateways."
        )
    )

    story.append(p("5. Database Changes", "SectionTitle"))
    story.append(p("The database was changed from a normal messaging schema into a SaaS schema."))
    story.append(bullets([
        "companies table: stores each tenant company.",
        "subscriptions table: stores the plan, status, seats, price, start date, and end date for each company.",
        "modules table: stores available application modules.",
        "company_module table: stores which modules are enabled for each company.",
        "users.company_id: connects users to a company.",
        "users.role: stores employee, company_admin, or super_admin.",
        "messages.company_id: connects every message to one company.",
    ]))
    story.append(code("companies\nsubscriptions\nmodules\ncompany_module\nusers.company_id\nmessages.company_id"))

    story.append(p("6. Model Relationships", "SectionTitle"))
    story.append(p("New Eloquent models and relationships were added to represent SaaS ownership."))
    story.append(bullets([
        "Company has one Subscription.",
        "Company has many Users.",
        "Company has many Messages.",
        "Company belongs to many Modules.",
        "User belongs to Company.",
        "Message belongs to Company, Sender, and Recipient.",
    ]))

    story.append(p("7. Controller Logic", "SectionTitle"))
    story.append(p("The MessageController was changed so the page works inside the selected company tenant."))
    story.append(bullets([
        "The selected company is read from company_id in the query string.",
        "Only users from the selected company are loaded.",
        "Only inbox and sent messages from the selected company are loaded.",
        "A message can only be sent if sender and recipient belong to the same company.",
        "A message can only be sent if the company is active or trialing.",
        "A message can only be sent if the subscription is active or trialing.",
        "A message can only be sent if the Messaging module is enabled.",
    ]))
    story.append(code("abort_unless($message->company_id === $user->company_id && $message->recipient_id === $userId, 403);"))

    story.append(p("8. Super Admin Control", "SectionTitle"))
    story.append(p("A Super Admin console was added to the page. It can manage company status and enabled modules."))
    story.append(bullets([
        "The Super Admin can see all companies.",
        "The Super Admin can see user and message counts per company.",
        "The Super Admin can change company status: active, trialing, or suspended.",
        "The Super Admin can enable or disable modules per company.",
    ]))

    story.append(p("9. Multi-Modules Implementation", "SectionTitle"))
    story.append(
        p(
            "The project demonstrates modules through a modules table and a company_module pivot table. Each company can have different modules enabled. For example, one company can have Messaging, Subscriptions, Reports, and Company Management, while another company can have only Messaging and Subscriptions."
        )
    )
    story.append(
        p(
            "In a larger production project, the package nwidart/laravel-modules could be installed to physically separate module files into folders such as Modules/Messaging, Modules/Subscriptions, and Modules/Admin."
        )
    )

    story.append(p("10. Verification", "SectionTitle"))
    story.append(p("The project was verified after the SaaS conversion."))
    story.append(bullets([
        "Database migrations ran successfully.",
        "Demo companies, subscriptions, modules, users, and messages were seeded.",
        "The browser showed the SaaS dashboard with Super Admin console and tenant workspace.",
        "Automated tests passed: 5 tests, 5 passed.",
        "A test confirms users cannot send messages across different companies.",
    ]))

    preview_path = make_screenshot_preview()
    if preview_path:
        story.append(PageBreak())
        story.append(p("11. Screenshot", "SectionTitle"))
        story.append(p("The following screenshot shows the converted SaaS dashboard running locally."))
        story.append(Image(str(preview_path), width=6.8 * inch, height=4.4 * inch))

    story.append(p("12. Conclusion", "SectionTitle"))
    story.append(
        p(
            "The original Laravel project was a normal internal messaging system. It was converted into a basic Cloud SaaS application by adding companies as tenants, separating users and messages with company_id, adding subscriptions, adding modules, and adding a Super Admin interface to control companies. This fulfills the assignment concept of multi-tenant and multi-module SaaS in Laravel."
        )
    )

    story.append(p("Project Path", "SubTitle"))
    story.append(p(str(PROJECT_DIR), "Small"))

    def footer(canvas, doc):
        canvas.saveState()
        canvas.setFont("Helvetica", 8)
        canvas.setFillColor(colors.HexColor("#667085"))
        canvas.drawString(0.65 * inch, 0.35 * inch, "Laravel SaaS Assignment Report")
        canvas.drawRightString(A4[0] - 0.65 * inch, 0.35 * inch, f"Page {doc.page}")
        canvas.restoreState()

    doc.build(story, onFirstPage=footer, onLaterPages=footer)


if __name__ == "__main__":
    build_report()
    print(PDF_PATH)
