import * as React from "react";
import { Slot } from "@radix-ui/react-slot";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/utils";

const cyberButtonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg text-sm font-medium font-orbitron transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 relative overflow-hidden",
  {
    variants: {
      variant: {
        default: "glass-card neon-primary hover:shadow-neon-primary transform hover:scale-105",
        hologram: "holographic-border bg-transparent text-gradient-hologram hover:scale-110 hover-lift",
        cyber: "bg-primary text-primary-foreground neon-primary hover:bg-primary/90 scan-line",
        matrix: "bg-accent text-accent-foreground neon-accent matrix-text hover:animate-pulse-glow",
        ghost: "bg-transparent border border-card-border/30 hover:glass-card hover:neon-secondary",
        destructive: "bg-destructive text-destructive-foreground hover:bg-destructive/90 neon-destructive",
        outline: "border border-primary/30 bg-transparent text-primary hover:glass-card hover:neon-primary",
        secondary: "bg-secondary text-secondary-foreground neon-secondary hover:bg-secondary/80",
      },
      size: {
        default: "h-12 px-6 py-3",
        sm: "h-10 px-4 py-2 text-xs",
        lg: "h-14 px-8 py-4 text-base",
        xl: "h-16 px-10 py-5 text-lg",
        icon: "h-12 w-12",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  }
);

export interface CyberButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof cyberButtonVariants> {
  asChild?: boolean;
  glow?: boolean;
}

const CyberButton = React.forwardRef<HTMLButtonElement, CyberButtonProps>(
  ({ className, variant, size, asChild = false, glow = false, children, ...props }, ref) => {
    const Comp = asChild ? Slot : "button";
    
    return (
      <Comp
        className={cn(
          cyberButtonVariants({ variant, size, className }),
          glow && "animate-pulse-glow"
        )}
        ref={ref}
        {...props}
      >
        {children}
        {/* Scan line effect */}
        {variant === "cyber" && (
          <div className="absolute inset-0 opacity-0 hover:opacity-100 transition-opacity duration-300">
            <div className="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-primary-glow to-transparent animate-scan"></div>
          </div>
        )}
      </Comp>
    );
  }
);
CyberButton.displayName = "CyberButton";

export { CyberButton, cyberButtonVariants };