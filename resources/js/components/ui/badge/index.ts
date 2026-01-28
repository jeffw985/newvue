import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Badge } from "./Badge.vue"

export const badgeVariants = cva(
  "inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden",
  {
    variants: {
      variant: {
        default:
          "border-transparent bg-primary text-primary-foreground [a&]:hover:bg-primary/90",
        secondary:
          "border-transparent bg-secondary text-secondary-foreground [a&]:hover:bg-secondary/90",
        destructive:
         "border-transparent bg-destructive text-white [a&]:hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60",
        outline:
          "text-foreground [a&]:hover:bg-accent [a&]:hover:text-accent-foreground",
        blue:
          "border-transparent bg-blue-500 text-white [a&]:hover:bg-blue-600 dark:bg-blue-600 dark:[a&]:hover:bg-blue-700",
        green:
          "border-transparent bg-green-500 text-white [a&]:hover:bg-green-600 dark:bg-green-600 dark:[a&]:hover:bg-green-700",
        "outline-blue":
          "border-blue-500/30 text-blue-600/90 [a&]:hover:bg-blue-50 dark:border-blue-400/40 dark:text-blue-400/90 dark:[a&]:hover:bg-blue-950",
        "outline-green":
          "border-green-500/30 text-green-600/90 [a&]:hover:bg-green-50 dark:border-green-400/40 dark:text-green-400/90 dark:[a&]:hover:bg-green-950",
        "outline-amber":
          "border-amber-500/30 text-amber-600/90 [a&]:hover:bg-amber-50 dark:border-amber-400/40 dark:text-amber-400/90 dark:[a&]:hover:bg-amber-950",
        "outline-gray":
          "border-gray-500/30 text-gray-600/90 [a&]:hover:bg-gray-50 dark:border-gray-400/40 dark:text-gray-400/90 dark:[a&]:hover:bg-gray-950",
        "outline-purple":
          "border-purple-500/30 text-purple-600/90 [a&]:hover:bg-purple-50 dark:border-purple-400/40 dark:text-purple-400/90 dark:[a&]:hover:bg-purple-950",
        "outline-red":
          "border-red-500/30 text-red-600/90 [a&]:hover:bg-red-50 dark:border-red-400/40 dark:text-red-400/90 dark:[a&]:hover:bg-red-950",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
)
export type BadgeVariants = VariantProps<typeof badgeVariants>
